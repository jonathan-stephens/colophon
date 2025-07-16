<script lang="ts">
  const {
    onclick,
    onmouseenter,
    onmouseout,
    onblur,
    active = false,
    type = "button",
    style = "",
    disabled = false,
    ariaLabel = "",
    id = "",
    ariaHaspopup = "",
    ariaExpanded = "",
    ariaControls = "",
  }: {
    onclick?: () => void;
    onmouseenter?: () => void;
    onmouseout?: () => void;
    onblur?: () => void;
    active?: boolean;
    type?: "reset" | "submit" | "button";
    style?: string;
    disabled?: boolean;
    ariaLabel?: string;
    id?: string;
    ariaHaspopup?: string;
    ariaExpanded?: string;
    ariaControls?: string;
  } = $props();
</script>

<button
  {onclick}
  class="button {style}"
  {type}
  class:is-active={active}
  aria-label={ariaLabel}
  {id}
  aria-haspopup={ariaHaspopup === "menu" ? "menu" : null}
  aria-expanded={ariaExpanded === "true" ? true : ariaExpanded === "false" ? false : null}
  aria-controls={ariaControls || null}
  {disabled}
  {onmouseenter}
  {onmouseout}
  {onblur}
>
  <slot name="icon" />
  {#if $$slots.default}<span><slot /></span>{/if}
</button>

<style>
  button {
    appearance: none;
    background-color: var(--button-background);
    color: var(--button-color);
    padding: var(--button-padding);
    border: 0;
    font-family: var(--font-family);
    letter-spacing: 0.01em;
    border-radius: var(--button-border-radius);
    display: inline-flex;
    gap: var(--button-gap);
    align-items: center;
    cursor: pointer;
    font-size: var(--button-font-size);
    justify-content: center;
    flex: 0 0 auto;
    font-weight: var(--button-font-weight);
    transition: var(--button-transition);
    white-space: nowrap;
    line-height: 1;
    height: var(--button-height);
    outline-color: var(--button-outline-color);

    &:focus-visible {
      outline-offset: var(--button-outline-offset);
    }

    &:hover,
    &:focus-visible {
      color: var(--button-hover-color);
      background-color: var(--button-hover-background);
    }

    span {
      text-overflow: ellipsis;
      overflow-x: clip;
      display: block;
      min-width: 0;
    }

    &.button--header {
      --icon-size: 1.25rem;
      background-color: var(--button-header-background);
      height: var(--button-header-height);
      padding: var(--button-header-padding);
      border-radius: 0;
      border: 0;
      mix-blend-mode: var(--button-header-blend-mode);

      &:first-child {
        border-top-left-radius: var(--border-radius-rounded);
        border-bottom-left-radius: var(--border-radius-rounded);
      }

      &:hover,
      &:focus-visible {
        background-color: var(--button-header-hover-background);
      }
    }

    &.button--panel {
      background-color: var(--button-panel-background);
      height: auto;
      padding: var(--button-panel-padding);
      border-radius: var(--border-radius-rounded);
      border: 0;
      span {
        overflow: visible;
      }
    }

    &.button--solid {
      background-color: var(--button-solid-background);
      &:hover,
      &:focus-visible {
        color: var(--button-solid-hover-color);
        background-color: var(--button-solid-hover-background);
      }
    }

    &.button--small {
      height: var(--button-small-height);
      font-size: var(--button-small-font-size);
    }

    &.button--icon {
      background-color: var(--button-icon-background);
      color: var(--button-icon-color);
      height: var(--button-icon-height);
      box-shadow: var(--button-icon-shadow);
      aspect-ratio: 1;
      padding: 0;
      font-size: var(--button-icon-font-size);
      border-radius: var(--button-icon-border-radius);
      border: 0;
      &:hover,
      &:focus-visible {
        background-color: var(--button-icon-hover-background);
        color: var(--button-icon-hover-color);
      }
    }

    &.button--marker {
      background-color: var(--button-marker-background);
      color: var(--button-marker-color);
      padding: 0;
      height: var(--marker-size);
      width: var(--marker-size);
      font-weight: var(--button-marker-font-weight);
      border-radius: var(--button-marker-border-radius);
      border: 0;

      * {
        pointer-events: none;
      }

      &.button--marker-highlighted {
        background-color: var(--button-marker-highlighted-background);
        color: var(--button-marker-highlighted-color);
      }
    }

    &.button--marker-open {
      background-color: var(--color-accent);
      color: var(--color-accent-dark);
    }

    &.button--filter {
      background-color: var(--button-filter-background);
      color: var(--button-filter-color);
      height: var(--button-filter-height);
      flex: 1;
      font-size: var(--button-filter-font-size);
      padding: var(--button-filter-padding);
      border-radius: var(--button-filter-border-radius);

      &:hover,
      &:focus-visible {
        color: var(--button-filter-hover-color);
        background-color: var(--button-filter-hover-background);
      }

      &.button--filter-active {
        background-color: var(--button-filter-active-background);
        color: var(--button-filter-active-color);
        font-weight: var(--button-filter-active-font-weight);

        &:hover,
        &:focus-visible {
          background-color: var(--button-filter-active-background);
          color: var(--button-filter-active-color);
        }
      }
    }

    &.button--menu-item {
      background-color: var(--button-menu-item-background);
      color: var(--button-menu-item-color);
      width: 100%;
      justify-content: flex-start;
      padding: var(--button-menu-item-padding);
      border-radius: var(--button-menu-item-border-radius);
      font-size: var(--button-menu-item-font-size);
      gap: var(--button-menu-item-gap);

      &:hover,
      &:focus-visible {
        background-color: var(--button-menu-item-hover-background);
        color: var(--button-menu-item-hover-color);
      }

      &.button--menu-item-active {
        background-color: var(--button-menu-item-active-background);
        color: var(--button-menu-item-active-color);
        font-weight: var(--button-menu-item-active-font-weight);

        &:hover,
        &:focus-visible {
          background-color: var(--button-menu-item-active-background);
          color: var(--button-menu-item-active-color);
        }
      }
    }

    &.is-active {
      background-color: var(--button-active-background);
      color: var(--button-active-color);
      &:hover,
      &:focus-visible {
        color: var(--button-active-color);
        background-color: var(--button-active-background);
      }
    }

    &:disabled {
      opacity: var(--button-disabled-opacity);
      cursor: not-allowed;
      &:hover {
        color: var(--button-disabled-hover-color);
        background-color: var(--button-disabled-hover-background);
      }
    }
  }
</style>
