/**
 * Bootstrap modals inside .main-content sit under body-level .modal-backdrop because
 * .main-content creates a low stacking context (z-index: 1). Move modals to
 * #dashboard-modal-portal (sibling of .main-content) so they stack and position correctly.
 */
(function () {
  'use strict';

  var PORTAL_ID = 'dashboard-modal-portal';

  function getPortal() {
    var portal = document.getElementById(PORTAL_ID);
    if (!portal) {
      portal = document.createElement('div');
      portal.id = PORTAL_ID;
      portal.setAttribute('aria-hidden', 'true');
      var anchor = document.querySelector('.dashboard-app .main-content');
      if (anchor && anchor.parentNode) {
        anchor.parentNode.insertBefore(portal, anchor.nextSibling);
      } else {
        document.body.appendChild(portal);
      }
    }
    return portal;
  }

  function isTrapped(modal) {
    return modal.classList.contains('modal') && modal.closest('.main-content');
  }

  function relocateModals() {
    var portal = getPortal();
    document.querySelectorAll('.modal').forEach(function (modal) {
      if (isTrapped(modal) && modal.parentElement !== portal) {
        portal.appendChild(modal);
      }
    });
  }

  function ensureCentered(dialog) {
    if (!dialog || dialog.classList.contains('modal-dialog-centered')) {
      return;
    }
    dialog.classList.add('modal-dialog-centered');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', relocateModals);
  } else {
    relocateModals();
  }

  document.addEventListener('show.bs.modal', function (event) {
    var modal = event.target;
    if (!modal || !modal.classList.contains('modal')) {
      return;
    }
    getPortal().appendChild(modal);
    ensureCentered(modal.querySelector('.modal-dialog'));
  }, true);

  var main = document.querySelector('.dashboard-app .main-content');
  if (main && typeof MutationObserver !== 'undefined') {
    new MutationObserver(relocateModals).observe(main, { childList: true, subtree: true });
  }
})();
