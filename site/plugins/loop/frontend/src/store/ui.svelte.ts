export const panel = $state({
  open: false,
  currentCommentId: 0,
  showResolvedOnly: false,
  pulseMarkerId: 0
});
export const overlay = $state({ open: false });

// Guest name management
let guestNameValue = $state("");

export const guestName = {
  get value() {
    return guestNameValue;
  },
  set(name: string) {
    guestNameValue = name;
    if (typeof window !== 'undefined') {
      sessionStorage.setItem('loop-guest-name', name);
    }
  },
  get() {
    if (!guestNameValue && typeof window !== 'undefined') {
      guestNameValue = sessionStorage.getItem('loop-guest-name') || "";
    }
    return guestNameValue;
  },
  clear() {
    guestNameValue = "";
    if (typeof window !== 'undefined') {
      sessionStorage.removeItem('loop-guest-name');
    }
  }
};
