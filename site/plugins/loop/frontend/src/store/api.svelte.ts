import type { Comment, CommentPayload, Reply, ReplyPayload } from '../types';

export const store: { comments: Comment[] } = $state({
  comments: []
});

const apiPrefix = 'loop';
const KirbyLoop = document.querySelector('kirby-loop');
const csrfToken = KirbyLoop?.getAttribute('csrf-token') || '';
const apiBase = KirbyLoop?.getAttribute('apibase') || '/';
const headers = {
  'Content-Type': 'application/json',
  'X-CSRF-Token': csrfToken || ''
};

const buildApiUrl = (endpoint: string): string => {
  const url = new URL(`${apiBase}/${apiPrefix}/${endpoint}`, window.location.origin);
  
  // Add token query params from current page if they exist
  const currentParams = new URLSearchParams(window.location.search);
  const token = currentParams.get('token') || currentParams.get('_token');
  if (token) {
    url.searchParams.set(currentParams.has('token') ? 'token' : '_token', token);
  }
  
  return url.toString();
};

export const getComments = async (pageId: string): Promise<boolean> => {
  const url = buildApiUrl(`comments/${pageId}`);
  const response = await fetch(url, {
    headers
  });
  const data = await response.json();
  if (data.status === 'ok') {
    store.comments = data.comments;
  }
  return data.status === 'ok';
}

export const addComment = async (comment: CommentPayload) => {
  const url = buildApiUrl('comment/new');
  const response = await fetch(url, {
    method: 'POST',
    headers,
    body: JSON.stringify(comment)
  });
  const data: { comment: Comment, status: string } = await response.json();
  if (data.status === 'ok') {
    store.comments = [data.comment, ...store.comments];
  }
}

export const resolveComment = async (comment: Comment) => {
  const url = buildApiUrl('comment/resolve');
  const response = await fetch(url, {
    method: 'POST',
    headers,
    body: JSON.stringify({ id: comment.id })
  });
  const data: { success: boolean } = await response.json();
  if (data.success) {
    const commentIndex = store.comments.findIndex(c => c.id === comment.id);
    if (commentIndex !== -1) {
      store.comments[commentIndex].status = 'RESOLVED';
    }
  }
  return data.success;
}

export const unresolveComment = async (comment: Comment) => {
  const url = buildApiUrl('comment/unresolve');
  const response = await fetch(url, {
    method: 'POST',
    headers,
    body: JSON.stringify({ id: comment.id })
  });
  const data: { success: boolean } = await response.json();
  if (data.success) {
    const commentIndex = store.comments.findIndex(c => c.id === comment.id);
    if (commentIndex !== -1) {
      store.comments[commentIndex].status = 'OPEN';
    }
  }
  return data.success;
}

export const setGuestName = async (name: string) => {
  const response = await fetch(buildApiUrl('guest/name'), {
    method: 'POST',
    headers,
    body: JSON.stringify({ name })
  });
  return await response.json();
}

export const addReply = async (reply: ReplyPayload) => {
  const url = buildApiUrl('comment/reply');
  const response = await fetch(url, {
    method: 'POST',
    headers,
    body: JSON.stringify(reply)
  });
  const data: { reply: Reply, status: string } = await response.json();
  if (data.status === 'ok') {
    const parent = store.comments.find(c => c.id === data.reply.parentId)
    if (parent) parent.replies = [...parent.replies, data.reply];
  }
}

export default store;
