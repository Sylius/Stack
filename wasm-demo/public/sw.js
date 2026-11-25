// The web server of the demo: PHP, as WebAssembly, runs the Sylius Stack test
// application in this service worker, and answers every request of its scope.
// The browser stops it after 30 s without a request, and PHP with it: the next
// request starts PHP again, that finds the database and the sessions in
// IndexedDB.

import createPhp, { serveInServiceWorker } from './build/php-web.mjs';
// The name of the build, written by castor demo:build: a new build is a new
// service worker, and a new .wasm, not the ones the browser has
import version from './build/version.js';

// Written by castor demo:embed, keep in sync with ../castor.php and
// ../overlay/.env
const DATA = '/app/data';
const PERSISTED = '/stack';

serveInServiceWorker(async () => {
    const php = await createPhp({
        persist: PERSISTED,
        locateFile: (path, directory) => `${directory}${path}?v=${version}`,
    });

    // A new build comes with a new database, with the fixtures. Done in PHP,
    // not with php.FS: it does not see what IndexedDB has before the first run
    const { exitCode, stdout } = await php.runCode(`
        if (@file_get_contents('${PERSISTED}/build-id') !== file_get_contents('${DATA}/build-id')) {
            @mkdir('${PERSISTED}/sessions', 0777, true);
            copy('${DATA}/demo.db', '${PERSISTED}/data.db');
            copy('${DATA}/build-id', '${PERSISTED}/build-id');
        }
    `);
    if (0 !== exitCode) {
        throw new Error(`The database is not created: ${stdout}`);
    }

    return php;
}, {
    documentRoot: '/app/public',
    // Served by the web server, not by PHP: see castor demo:embed
    except: ['build/', 'assets/', 'bundles/', 'avatars/'],
});
