/**
 * Decodes HTML entities in a string
 * @param text The text that may contain HTML entities
 * @returns The decoded text
 */
export function decodeHTMLEntities(text: string): string {
  const entityMap: Record<string, string> = {
    '&amp;': '&',
    '&lt;': '<',
    '&gt;': '>',
    '&quot;': '"',
    '&#x27;': "'",
    '&#x2F;': '/',
    '&#x60;': '`',
    '&#x3D;': '='
  };

  return text.replace(/&[#\w]+;/g, (entity) => entityMap[entity] || entity);
}
