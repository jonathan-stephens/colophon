<script lang="ts">
  const { initials } = $props();
</script>

<div class="author">
  {initials}
</div>

<style scoped>
  .author {
    font-size: var(--author-avatar-font-size);
    text-transform: uppercase;
    color: var(--author-avatar-color);
    background-color: var(--author-avatar-background-color);
    aspect-ratio: 1;
    flex: 0 0 var(--author-avatar-size);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--author-avatar-border-radius);
  }
</style>
