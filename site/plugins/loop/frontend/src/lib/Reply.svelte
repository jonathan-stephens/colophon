<script lang="ts">
  import type { Reply } from "../types";
  import Author from "./Author.svelte";
  import { t } from "../store/translations.svelte";
  import { formatDate } from "../composables/formatDate";
  import { formatDateISO } from "../composables/formatDateISO";
  import { decodeHTMLEntities } from "../composables/decodeHTMLEntities";
  export let reply: Reply;
</script>

<article
  class="reply"
  data-id={reply.id}
  aria-label="{t(
    'ui.reply.aria.label',
    'Reply by',
  )} {reply.author}: {decodeHTMLEntities(reply.comment)}"
>
  <Author initials={reply.author.substring(0, 1)} />
  <div class="reply__content">
    <header>
      <strong>{reply.author}</strong>
      <time
        datetime={formatDateISO(reply.timestamp)}
        title={formatDate(reply.timestamp, false)}
      >
        {formatDate(reply.timestamp)}
      </time>
    </header>
    <div class="reply__text">{decodeHTMLEntities(reply.comment)}</div>
  </div>
</article>

<style>
  .reply {
    display: flex;
    gap: var(--reply-gap);
    flex-direction: row;
    align-items: start;
  }

  .reply__content {
    padding: var(--reply-content-padding);
    background-color: var(--reply-content-background);
    border-radius: var(--reply-content-border-radius);

    header {
      display: flex;
      gap: var(--reply-header-gap);
      align-items: center;
      justify-content: flex-start;
      margin-bottom: var(--reply-header-margin-bottom);
      time {
        font-size: var(--reply-timestamp-font-size);
        color: var(--reply-timestamp-color);
      }
    }

    @media (prefers-color-scheme: dark) {
      background-color: var(--reply-content-background-dark);
    }
  }

  .reply__text {
    white-space: pre-line;
  }
</style>
