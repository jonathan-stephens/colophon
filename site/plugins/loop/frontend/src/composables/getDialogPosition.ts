import { getDocumentHeight } from "./getDocumentHeight";
import { getDocumentWidth } from "./getDocumentWidth";

export const getDialogPosition = (marker: { pagePositionX: number, pagePositionY: number } | null,
  dialogElement: HTMLDialogElement | null): { left: number, top: number } => {
  // Default position (fallback)
  let left = 0;
  let top = 0;

  if (!marker || !dialogElement) return { left, top };

  // Get marker position
  left = marker.pagePositionX;
  top = marker.pagePositionY;

  // Get dialog dimensions
  const dialogWidth = dialogElement.offsetWidth;
  const dialogHeight = dialogElement.offsetHeight;

  // Get document dimensions
  const documentWidth = getDocumentWidth();
  const documentHeight = getDocumentHeight();

  // Ensure dialog doesn't go off-screen to the right
  if (left + dialogWidth > documentWidth) {
    left = documentWidth - dialogWidth;
  }

  // Ensure dialog doesn't go off-screen to the bottom
  if (top + dialogHeight > documentHeight) {
    top = documentHeight - dialogHeight;
  }

  // Ensure dialog doesn't go off-screen to the left or top
  left = Math.max(0, left);
  top = Math.max(0, top);

  return { left, top };
}
