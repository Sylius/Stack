/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
import "@tabler/core/dist/css/tabler.min.css"
import "tom-select/dist/css/tom-select.bootstrap5.css";

import './styles/main.css';

import './scripts/bulk-delete.js';
import './scripts/check-all.js';
import './scripts/menu-search.js';

import './scripts/bootstrap.js';

import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();