// Get the entire document width, including scrollable area
export const getDocumentWidth = (): number => {
  const body = document.body;
  const html = document.documentElement;

  return Math.max(
    body.scrollWidth,
    body.offsetWidth,
    html.clientWidth,
    html.scrollWidth,
    html.offsetWidth
  );
};
