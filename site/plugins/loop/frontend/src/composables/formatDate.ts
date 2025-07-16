import { t, tt } from "../store/translations.svelte";

export function formatDate(timestamp: number, humanize = true): string {
  const date = new Date(timestamp * 1000);
  const now = new Date();
  const diffInMs = now.getTime() - date.getTime();
  const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

  // Show relative time for up to 3 days
  if (humanize && diffInDays <= 3) {
    if (diffInMinutes < 1) {
      return t("ui.time.just_now", "just now");
    } else if (diffInMinutes === 1) {
      return t("ui.time.minute_ago", "a minute ago");
    } else if (diffInMinutes < 60) {
      return tt("ui.time.minutes_ago", "{count} minutes ago", { count: diffInMinutes.toString() });
    } else if (diffInHours === 1) {
      return t("ui.time.hour_ago", "an hour ago");
    } else if (diffInHours < 24) {
      return tt("ui.time.hours_ago", "{count} hours ago", { count: diffInHours.toString() });
    } else if (diffInDays === 1) {
      return t("ui.time.yesterday", "yesterday");
    } else {
      return tt("ui.time.days_ago", "{count} days ago", { count: diffInDays.toString() });
    }
  }

  return date.toLocaleString(undefined, { dateStyle: "short", timeStyle: "short" });
}
