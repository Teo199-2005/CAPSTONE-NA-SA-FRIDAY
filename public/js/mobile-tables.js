/**
 * Dashboard mobile tables: set data-label on cells from <thead> for card-style layout.
 * Re-runs on load and when new tables are injected (debounced).
 */
(function () {
  'use strict';

  var ROOT = '.dashboard-app';
  var SKIP = 'table-mobile-static';

  function expandHeaderCells(headerRow) {
    var labels = [];
    if (!headerRow) {
      return labels;
    }
    var cells = headerRow.querySelectorAll('th, td');
    cells.forEach(function (cell) {
      var span = parseInt(cell.getAttribute('colspan') || '1', 10);
      if (isNaN(span) || span < 1) {
        span = 1;
      }
      var text = cell.getAttribute('data-col-label') || '';
      if (!text) {
        text = (cell.textContent || '').replace(/\s+/g, ' ').trim();
      }
      if (!text) {
        text = 'Field';
      }
      for (var i = 0; i < span; i++) {
        labels.push(text);
      }
    });
    return labels;
  }

  function directThead(table) {
    for (var i = 0; i < table.children.length; i++) {
      if (table.children[i].tagName === 'THEAD') {
        return table.children[i];
      }
    }
    return null;
  }

  function getHeaderLabels(table) {
    var thead = directThead(table);
    if (!thead) {
      return [];
    }
    var rows = thead.querySelectorAll('tr');
    var headerRow = rows.length ? rows[rows.length - 1] : null;
    return expandHeaderCells(headerRow);
  }

  function labelTable(table) {
    if (!table || table.classList.contains(SKIP)) {
      return;
    }

    var headerTexts = getHeaderLabels(table);
    if (!headerTexts.length) {
      table.classList.add(SKIP);
      table.setAttribute('data-mobile-skip', 'no-thead');
      return;
    }
    if (table.getAttribute('data-mobile-skip') === 'no-thead') {
      table.classList.remove(SKIP);
      table.removeAttribute('data-mobile-skip');
    }

    Array.prototype.forEach.call(table.tBodies, function (tbody) {
      Array.prototype.forEach.call(tbody.rows, function (row) {
        if (row.parentElement !== tbody) {
          return;
        }
        var tds = row.getElementsByTagName('td');
        Array.prototype.forEach.call(tds, function (cell, index) {
          var label = headerTexts[index];
          if (label) {
            cell.setAttribute('data-label', label);
          }
        });
      });
    });
  }

  function countDirectActionButtons(container) {
    var n = 0;
    for (var i = 0; i < container.children.length; i++) {
      var el = container.children[i];
      if (!el || !el.tagName) {
        continue;
      }
      var tag = el.tagName.toUpperCase();
      if (tag === 'A' && el.classList && el.classList.contains('btn')) {
        n++;
        continue;
      }
      if (tag === 'BUTTON' && (el.classList.contains('btn') || el.type === 'submit')) {
        n++;
      }
    }
    return n;
  }

  function countButtonsInForm(form) {
    return form.querySelectorAll('button.btn, button[type="submit"], a.btn, .btn').length;
  }

  /** Card-style action area: set data-mobile-action-count for 2×2 / 3×3 CSS */
  function setMobileActionButtonLayouts() {
    var tds = document.querySelectorAll(
      '.dashboard-app table:not(.table-mobile-static) > tbody > tr > td:last-child, ' +
        '.dashboard-app table:not(.table-mobile-static) > tfoot > tr > td:last-child'
    );
    for (var i = 0; i < tds.length; i++) {
      var td = tds[i];
      td.removeAttribute('data-mobile-action-count');

      var c;
      for (c = 0; c < td.children.length; c++) {
        var ch = td.children[c];
        if (ch.classList && (ch.classList.contains('btn-group') || ch.classList.contains('table-actions-grid'))) {
          ch.removeAttribute('data-mobile-action-count');
        }
        if (ch.tagName === 'FORM') {
          ch.removeAttribute('data-mobile-action-count');
        }
      }

      var childGroup = null;
      for (c = 0; c < td.children.length; c++) {
        if (
          td.children[c].classList
          && (td.children[c].classList.contains('btn-group') || td.children[c].classList.contains('table-actions-grid'))
        ) {
          childGroup = td.children[c];
          break;
        }
      }

      if (childGroup) {
        var gn = countDirectActionButtons(childGroup);
        if (gn > 0) {
          childGroup.setAttribute('data-mobile-action-count', String(gn));
        }
        continue;
      }

      if (td.children.length === 1 && td.children[0].tagName === 'FORM') {
        var form = td.children[0];
        var fn = countButtonsInForm(form);
        if (fn > 0) {
          form.setAttribute('data-mobile-action-count', String(fn));
        }
        continue;
      }

      var loose = countDirectActionButtons(td);
      if (loose > 0) {
        td.setAttribute('data-mobile-action-count', String(loose));
      }
    }
  }

  function applyAll() {
    document.querySelectorAll(ROOT + ' table').forEach(labelTable);
    setMobileActionButtonLayouts();
  }

  function debounce(fn, ms) {
    var t;
    return function () {
      clearTimeout(t);
      t = setTimeout(fn, ms);
    };
  }

  document.addEventListener('DOMContentLoaded', function () {
    applyAll();
  });

  window.addEventListener('load', function () {
    applyAll();
  });

  var rootEl = document.querySelector(ROOT);
  if (rootEl && typeof MutationObserver !== 'undefined') {
    var schedule = debounce(applyAll, 200);
    var observer = new MutationObserver(function (mutations) {
      for (var i = 0; i < mutations.length; i++) {
        if (mutations[i].addedNodes && mutations[i].addedNodes.length) {
          schedule();
          return;
        }
      }
    });
    observer.observe(rootEl, { childList: true, subtree: true });
  }

  window.applyDashboardMobileTableLabels = applyAll;
  window.applyDashboardMobileTableActionLayouts = setMobileActionButtonLayouts;
})();
