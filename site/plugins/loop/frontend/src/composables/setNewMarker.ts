import { useGenerateSelector } from "./useGenerateSelector";
import { getSelectorOffset } from "./getSelectorOffset";

export const setNewMarker = (e: MouseEvent) => {

  const selector = useGenerateSelector(e);
  const element: HTMLElement | null = document.querySelector(selector);

  // error out, if no selector found
  if (!element) return;

  const { selectorOffsetX, selectorOffsetY } = getSelectorOffset(e, element);

  // Store absolute position on the page
  let pagePositionX = e.pageX;
  let pagePositionY = e.pageY;

  // Round to 2 digits
  pagePositionX = Number(pagePositionX.toFixed(2));
  pagePositionY = Number(pagePositionY.toFixed(2));

  return {
    selector,
    selectorOffsetX,
    selectorOffsetY,
    pagePositionX,
    pagePositionY
  }

}

export default setNewMarker;
