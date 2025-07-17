export const getSelectorOffset = (e: MouseEvent, element: HTMLElement): { selectorOffsetX: number, selectorOffsetY: number } => {
  // Get absolute click position (relative to the document)
  const clickX = e.pageX;
  const clickY = e.pageY;

  // Get element's position relative to the document
  const rect = element.getBoundingClientRect();
  const elementX = rect.left + window.scrollX;
  const elementY = rect.top + window.scrollY;

  // Calculate relative offsets
  const offsetXRel = clickX - elementX;
  const offsetYRel = clickY - elementY;

  // Convert to percentages
  let offsetX = (offsetXRel / element.offsetWidth) * 100;
  let offsetY = (offsetYRel / element.offsetHeight) * 100;

  // Round to 2 decimal places
  offsetX = Number(offsetX.toFixed(2));
  offsetY = Number(offsetY.toFixed(2));

  return {
    selectorOffsetX: offsetX, selectorOffsetY: offsetY
  };
}
