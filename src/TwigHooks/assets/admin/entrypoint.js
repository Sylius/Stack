import '../../../../assets/admin/entrypoint';
import { createPopper } from '@popperjs/core';

// Globalny przełącznik mechanizmu tooltipów
let tooltipEnabled = false;

// Nasłuchujemy skrótu klawiszowego (Ctrl/Cmd+Shift+K) do włączania/wyłączania tooltipów
document.addEventListener('keydown', function(e) {
  if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === 'k') {
    tooltipEnabled = !tooltipEnabled;
    console.log(`Tooltips are now ${tooltipEnabled ? 'enabled' : 'disabled'}`);
    // Jeśli wyłączamy tooltipy, usuwamy wszystkie widoczne tooltipy...
    if (!tooltipEnabled) {
      document.querySelectorAll('.custom-tooltip').forEach(t => t.remove());
      // ...oraz usuwamy obramowania ze wszystkich elementów [data-hook]
      document.querySelectorAll('[data-hook]').forEach(el => {
        el.style.boxShadow = '';
      });
    } else {
      // Gdy tooltipy zostały włączone, sprawdzamy elementy, które są już hoverowane
      document.querySelectorAll('[data-hook]').forEach(el => {
        if (el.matches(':hover')) {
          // Wywołujemy syntetyczne zdarzenie, aby natychmiast pokazać tooltip
          el.dispatchEvent(new Event('mouseenter'));
        }
      });
    }
  }
});

// Numer do przydzielenia unikalnego ID tooltipowi
let nextTooltipId = 0;
// Globalna tablica przechowująca pozycje już wyrenderowanych tooltipów
const renderedTooltips = [];

/**
 * Generuje losowy kolor RGB w przedziale 50-205 dla każdego kanału.
 */
function generateRGBColor() {
  const r = Math.floor(Math.random() * 156 + 50);
  const g = Math.floor(Math.random() * 156 + 50);
  const b = Math.floor(Math.random() * 156 + 50);
  return `rgb(${r}, ${g}, ${b})`;
}

/**
 * Pobiera kolor tooltipa z atrybutu lub generuje go automatycznie,
 * zapisując wynik w atrybucie, by przy kolejnych wywołaniach użyć tego samego.
 */
function getTooltipColor(el) {
  let color = el.getAttribute('data-tooltip-color');
  if (!color) {
    color = generateRGBColor();
    el.setAttribute('data-tooltip-color', color);
  }
  return color;
}

/**
 * Oblicza kontrastujący kolor tekstu (biały lub czarny) na podstawie podanego koloru tła.
 */
function getContrastingTextColor(rgbStr) {
  const matches = rgbStr.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
  if (!matches) return '#fff';
  const r = parseInt(matches[1], 10);
  const g = parseInt(matches[2], 10);
  const b = parseInt(matches[3], 10);
  // Prosty wzór na jasność – im jaśniejszy, tym lepszy kontrast osiągniemy czarnym tekstem
  const brightness = (r * 299 + g * 587 + b * 114) / 1000;
  return brightness > 128 ? '#000' : '#fff';
}

/**
 * Sprawdza, czy tooltip, którego pozycję pobieramy (rect), koliduje
 * z którymś z już wyrenderowanych tooltipów. Jeśli tak – dodaje dodatkowy offset.
 */
function adjustTooltipPosition(tooltip) {
  const rect = tooltip.getBoundingClientRect();
  let additionalOffset = { x: 0, y: 0 };

  renderedTooltips.forEach(existing => {
    if (Math.abs(existing.top - rect.top) < 20 && Math.abs(existing.left - rect.left) < 20) {
      additionalOffset.x += 20;
      additionalOffset.y += 20;
    }
  });

  if (additionalOffset.x || additionalOffset.y) {
    tooltip.style.transform = `translate(${additionalOffset.x}px, ${additionalOffset.y}px)`;
  }

  const newRect = tooltip.getBoundingClientRect();
  renderedTooltips.push({ id: tooltip.dataset.tooltipId, top: newRect.top, left: newRect.left });
}

/**
 * Usuwa pozycję tooltipa z globalnej tablicy po jego usunięciu.
 */
function removeTooltipPosition(tooltipId) {
  const index = renderedTooltips.findIndex(entry => entry.id === tooltipId);
  if (index !== -1) {
    renderedTooltips.splice(index, 1);
  }
}

document.querySelectorAll('[data-hook]').forEach(el => {
  let tooltip;
  let hideTimeout;

  function showTooltip() {
    // Jeśli mechanizm tooltipów jest wyłączony, nie robimy nic
    if (!tooltipEnabled) return;

    // Anulujemy ewentualne ukrywanie
    clearTimeout(hideTimeout);

    // Jeżeli tooltip jeszcze nie istnieje, go tworzymy
    if (!tooltip) {
      tooltip = document.createElement('div');
      tooltip.className = 'custom-tooltip';
      tooltip.dataset.tooltipId = nextTooltipId++;

      // Pobieramy dane z atrybutów
      const hook = el.getAttribute('data-hook') || 'N/A';
      const hookable = el.getAttribute('data-hookable') || 'N/A';

      // Ładne formatowanie zawartości tooltipa
      tooltip.innerHTML = `
        <div class="tooltip-section">
          <div class="tooltip-line">
            <strong>Hook:</strong>
            <span class="copy" data-copy="Hook" title="Kliknij, aby skopiować">${hook}</span>
          </div>
          <div class="tooltip-line">
            <strong>Hookable:</strong>
            <span class="copy" data-copy="Hookable" title="Kliknij, aby skopiować">${hookable}</span>
          </div>
        </div>
        <div class="tooltip-copied" style="display:none;">Skopiowano!</div>
      `;

      // Ustawiamy kolor tooltipa i obramowania
      const color = getTooltipColor(el);
      tooltip.style.backgroundColor = color;
      tooltip.style.border = `2px solid ${color}`;
      tooltip.style.boxShadow = `0 0 5px ${color}`;
      // Ustawiamy kontrastujący kolor tekstu
      tooltip.style.color = getContrastingTextColor(color);

      document.body.appendChild(tooltip);

      // Inicjalizacja Popper.js – dynamiczne pozycjonowanie
      createPopper(el, tooltip, {
        placement: 'bottom',
        modifiers: [
          {
            name: 'offset',
            options: { offset: [0, 8] },
          },
          {
            name: 'preventOverflow',
            options: { boundary: 'viewport' },
          },
          {
            name: 'flip',
            options: { fallbackPlacements: ['top', 'right', 'left'] },
          },
        ],
      });

      // Po krótkim czasie korygujemy pozycję tooltipa
      setTimeout(() => {
        adjustTooltipPosition(tooltip);
      }, 50);

      // Tooltip nie znika natychmiast – gdy kursor wejdzie na tooltip, przerywamy ukrywanie
      tooltip.addEventListener('mouseenter', () => {
        clearTimeout(hideTimeout);
        tooltip.style.opacity = '1';
      });
      tooltip.addEventListener('mouseleave', hideTooltip);

      // Obsługa kopiowania – kliknięcie na element z klasą "copy"
      tooltip.querySelectorAll('.copy').forEach(span => {
        span.style.cursor = 'pointer';
        span.addEventListener('click', (e) => {
          e.stopPropagation();
          const textToCopy = span.textContent;
          navigator.clipboard.writeText(textToCopy).then(() => {
            const copiedDiv = tooltip.querySelector('.tooltip-copied');
            if (copiedDiv) {
              copiedDiv.textContent = `Skopiowano ${span.getAttribute('data-copy').toUpperCase()}`;
              copiedDiv.style.display = 'block';
              setTimeout(() => {
                copiedDiv.style.display = 'none';
              }, 1500);
            }
          }).catch(err => {
            console.error('Error copying text: ', err);
          });
        });
      });
    }
    tooltip.style.opacity = '1';
    const color = getTooltipColor(el);
    el.style.boxShadow = `0 0 0 3px ${color}, 0 0 10px 3px ${color}`;
  }

  function hideTooltip() {
    hideTimeout = setTimeout(() => {
      if (tooltip) {
        removeTooltipPosition(tooltip.dataset.tooltipId);
        tooltip.remove();
        tooltip = null;
      }
      el.style.boxShadow = '';
    }, 500);
  }

  el.addEventListener('mouseenter', showTooltip);
  el.addEventListener('mouseleave', hideTooltip);
});
