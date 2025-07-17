export function formatDateISO(timestamp: number): string {
  return new Date(timestamp * 1000).toISOString();
}