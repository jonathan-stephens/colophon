<script lang="ts">
  import Button from "./Button.svelte";
  import { guestName } from "../store/ui.svelte";
  import { setGuestName } from "../store/api.svelte";
  import { t } from "../store/translations.svelte";

  const {
    headline,
    text,
    authenticated,
    welcomeEnabled = true,
    onDismiss,
  }: {
    headline: string;
    text: string;
    authenticated: boolean;
    welcomeEnabled?: boolean;
    onDismiss?: () => void;
  } = $props();

  let dialog: HTMLDialogElement;
  let name = $state("");
  let isSubmitting = $state(false);

  export const showModal = () => dialog?.showModal();
  export const close = () => dialog?.close();

  async function handleSubmit(e: SubmitEvent) {
    e.preventDefault();

    if (!authenticated) {
      if (name.trim() && !isSubmitting) {
        isSubmitting = true;
        try {
          await setGuestName(name.trim());
          guestName.set(name.trim());
          dialog?.close();
        } catch (error) {
          console.error("Failed to set guest name:", error);
        } finally {
          isSubmitting = false;
        }
      }
    } else {
      // For authenticated users, mark as dismissed when submitting
      onDismiss?.();
      dialog?.close();
    }
  }

  function handleCancel() {
    dialog?.close();
  }

  function handleDialogClose() {
    // Reset form when dialog closes
    name = "";
    isSubmitting = false;
  }
</script>

<dialog bind:this={dialog} onclose={handleDialogClose}>
  <form onsubmit={handleSubmit}>
    {#if welcomeEnabled}
      <h2>{headline}</h2>
      <p class="welcome-text">{text}</p>
    {/if}

    {#if !authenticated}
      <div class="name-section" class:no-welcome={!welcomeEnabled}>
        <div class="input">
          <input
            bind:value={name}
            type="text"
            placeholder={t(
              "ui.welcome.guest.name.placeholder",
              "Enter your name",
            )}
            required
          />
        </div>
      </div>
    {/if}

    <footer>
      <Button type="submit" style="button--solid" disabled={isSubmitting}>
        {#if !authenticated}
          {isSubmitting ? "Saving..." : t("ui.welcome.continue", "Continue")}
        {:else}
          {t("ui.welcome.continue", "Continue")}
        {/if}
      </Button>
      <Button onclick={handleCancel} disabled={isSubmitting}>
        {t("ui.welcome.dismiss", "Dismiss")}
      </Button>
    </footer>
  </form>
</dialog>

<style>
  dialog {
    backdrop-filter: var(--welcome-dialog-backdrop-filter);
    border: var(--welcome-dialog-border);
    border-radius: var(--welcome-dialog-border-radius);
    box-shadow: var(--welcome-dialog-shadow);
    width: 100%;
    max-width: var(--welcome-dialog-max-width);
    padding: 0;
    background: var(--welcome-dialog-background);
    &::backdrop {
      background: var(--welcome-dialog-backdrop-background);
      backdrop-filter: var(--welcome-dialog-backdrop-backdrop-filter);
    }
  }

  form {
    padding: var(--welcome-dialog-form-padding);
  }

  h2 {
    margin: var(--welcome-dialog-title-margin);
    font-size: var(--welcome-dialog-title-font-size);
    color: var(--welcome-dialog-title-color);
    font-weight: var(--welcome-dialog-title-font-weight);
  }

  .welcome-text {
    margin: var(--welcome-dialog-text-margin);
    font-size: var(--welcome-dialog-text-font-size);
    color: var(--welcome-dialog-text-color);
    line-height: var(--welcome-dialog-text-line-height);
  }

  .name-section {
    margin-bottom: var(--welcome-dialog-name-section-margin);
  }

  .name-section.no-welcome {
    border-top: none;
    padding-top: 0;
  }

  input {
    width: 100%;
    border: var(--welcome-dialog-input-border);
    border-radius: var(--welcome-dialog-input-border-radius);
    padding: var(--welcome-dialog-input-padding);
    box-sizing: border-box;
    font-family: var(--welcome-dialog-input-font-family);
    font-size: var(--welcome-dialog-input-font-size);
    color: var(--welcome-dialog-input-color);
    background: var(--welcome-dialog-input-background);

    &:focus-visible {
      outline-color: var(--welcome-dialog-input-outline-color);
      outline-offset: var(--welcome-dialog-input-outline-offset);
    }
  }

  footer {
    display: flex;
    gap: var(--welcome-dialog-footer-gap);
  }

  footer :global(button) {
    flex: 1;
  }
</style>
