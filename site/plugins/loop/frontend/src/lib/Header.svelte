<script lang="ts">
  import { panel, overlay } from "../store/ui.svelte";
  import { t } from "../store/translations.svelte";
  import IconComment from "./Icon/IconComment.svelte";
  import Button from "./Button.svelte";
  import IconBrowse from "./Icon/IconBrowse.svelte";

  const {
    position,
    commentsCount,
  }: {
    position: "top" | "bottom";
    commentsCount: number;
  } = $props();
</script>

<header class:bottom={position === "bottom"}>
  <div class="toggle">
    <Button
      onclick={() => {
        overlay.open = false;
      }}
      active={!overlay.open}
      style="button--header"
    >
      <IconBrowse --size="1.5em" slot="icon" />
      {t("ui.header.browse.mode", "Browse")}
    </Button>
    <Button
      onclick={() => {
        overlay.open = true;
      }}
      style="button--header"
      active={overlay.open}
    >
      <IconComment --size="1.5em" slot="icon" />
      {t("ui.header.comment.mode", "Comment")}
    </Button>
  </div>
  <Button
    onclick={() => (panel.open = !panel.open)}
    style="button--panel"
    ariaLabel={`${commentsCount} ${t("ui.header.aria.count", "unresolved comments")}`}
  >
    <span class="count">{commentsCount}</span>
  </Button>
</header>

<style>
  .toggle {
    display: flex;
  }

  header {
    position: var(--header-position);
    top: var(--header-top);
    left: 50%;
    max-width: 100%;
    transform: var(--header-transform);
    color: var(--header-color);
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    border-radius: var(--header-border-radius);
    z-index: var(--header-z-index);
    backdrop-filter: var(--header-backdrop-filter);
    box-shadow: var(--shadow-l), var(--shadow-light-edge),
      var(--shadow-dark-edge);
    background: var(--header-background);

    &.bottom {
      top: auto;
      bottom: var(--header-bottom-position);
    }
  }

  .count {
    width: var(--header-count-size);
    height: var(--header-count-size);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--header-count-border-radius);
    backdrop-filter: var(--header-count-backdrop-filter);
    box-shadow: var(--shadow-s), var(--shadow-light-edge),
      var(--shadow-dark-edge);
    background: var(--header-count-background);
  }
</style>
