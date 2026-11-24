// Checks the demo without a browser: PHP as WebAssembly, in node, logs in,
// then follows the links of the admin panel from the dashboard, and fails on
// any page that does not answer. Run by castor demo:check, before a deploy.
//
//     node check.mjs public/build/php-node.mjs

import { pathToFileURL } from 'node:url';
import path from 'node:path';

// Keep in sync with castor.php and public/sw.js
const DOCUMENT_ROOT = '/app/public';
const ORIGIN = 'http://localhost';
const MAX_PAGES = 150;
// What changes the data or ends the session: not followed
const SKIPPED = [/\/logout/, /\/delete/, /\/bulk-delete/];

const build = path.resolve(process.argv[2] ?? 'public/build/php-node.mjs');
const { default: createPhp } = await import(pathToFileURL(build));
const php = await createPhp();

const { exitCode, stdout } = await php.runCode(`
    @mkdir('/stack/sessions', 0777, true);
    copy('/app/data/demo.db', '/stack/data.db');
`);
if (0 !== exitCode) {
    throw new Error(`The database is not created: ${stdout}`);
}

const cookies = new Map();
const decoder = new TextDecoder();

async function request(method, url, body = '') {
    const headers = { Origin: ORIGIN, Cookie: [...cookies].map(([name, value]) => `${name}=${value}`).join('; ') };
    if ('' !== body) {
        headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }
    const response = await php.request({ method, url: ORIGIN + url, headers, body }, { documentRoot: DOCUMENT_ROOT });
    for (const [name, value] of response.headers) {
        if ('set-cookie' === name.toLowerCase()) {
            const [pair] = value.split(';');
            const separator = pair.indexOf('=');
            cookies.set(pair.slice(0, separator), pair.slice(separator + 1));
        }
    }

    return { ...response, text: decoder.decode(response.body) };
}

const login = await request('GET', '/admin/login');
const token = login.text.match(/name="_csrf_admin_security_token" value="([^"]+)"/)?.[1];
if (200 !== login.status || undefined === token) {
    throw new Error(`The login page answers ${login.status}: ${login.stderr}`);
}
const loggedIn = await request('POST', '/admin/login_check', new URLSearchParams({
    _username: 'admin@example.com',
    _password: 'admin',
    _csrf_admin_security_token: token,
}).toString());
const location = loggedIn.headers.find(([name]) => 'location' === name.toLowerCase())?.[1] ?? '';
if (302 !== loggedIn.status || location.includes('/login')) {
    throw new Error(`The login fails (${loggedIn.status}, ${location}): ${loggedIn.stderr}`);
}

const queue = ['/admin/'];
const seen = new Set(queue);
const failures = [];
let visited = 0;
while (queue.length > 0 && visited < MAX_PAGES) {
    const url = queue.shift();
    visited++;
    const response = await request('GET', url);
    console.log(`${response.status} ${url}`);
    if (response.status >= 500) {
        failures.push(`${url}: ${response.stderr.split('\n').find((line) => line.includes('[critical]')) ?? response.stderr}`);
        continue;
    }

    for (const [, href] of response.text.matchAll(/href="(\/admin\/[^"#]*)"/g)) {
        const link = href.replaceAll('&amp;', '&');
        // Once per page: not each sorting, filter and page of a grid
        const page = link.split('?')[0];
        if (!seen.has(page) && !SKIPPED.some((pattern) => pattern.test(link))) {
            seen.add(page);
            queue.push(link);
        }
    }
}

if (failures.length > 0) {
    console.error(`\n${failures.length} page(s) fail:\n${failures.join('\n')}`);
    process.exit(1);
}
console.log(`\n${visited} pages answer.`);
