import {BroadcastMessage} from '@typo3/backend/broadcast-message';
import BroadcastService from '@typo3/backend/broadcast-service';
import RegularEvent from '@typo3/core/event/regular-event';
import Modal from '../modal';

enum MODIFIER_KEYS {
  META = 'Meta',
  CTRL = 'Control'
}

class LiveSearchShortcut {
  public constructor() {
    // navigator.platform is deprecated, but https://developer.mozilla.org/en-US/docs/Web/API/User-Agent_Client_Hints_API is experimental for now
    const expectedModifierKey = navigator.platform.toLowerCase().startsWith('mac') ? MODIFIER_KEYS.META : MODIFIER_KEYS.CTRL;

    new RegularEvent('keydown', (e: KeyboardEvent): void => {
      if (e.repeat) {
        return;
      }

      const modifierKeyIsDown = expectedModifierKey === MODIFIER_KEYS.META && e.metaKey || expectedModifierKey === MODIFIER_KEYS.CTRL && e.ctrlKey;
      if (modifierKeyIsDown && ['k', 'K'].includes(e.key)) {
        if (Modal.currentModal) {
          // A modal window is already active, keep default behavior of browser
          return;
        }

        e.preventDefault();

        document.dispatchEvent(new CustomEvent('typo3:live-search:trigger-open'));
        BroadcastService.post(new BroadcastMessage(
          'live-search',
          'trigger-open',
          {}
        ))
      }
    }).bindTo(document);
  }
}

export default new LiveSearchShortcut();
