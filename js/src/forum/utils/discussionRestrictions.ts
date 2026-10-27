import app from 'flarum/forum/app';
import type Discussion from 'flarum/common/models/Discussion';

export function allowedFeedbackTags(): string[] {
  try {
    const tags = JSON.parse(app.forum.attribute<string>('huseyinfiliz.traderfeedback.feedbackActionTagFilter') || '[]');
    return Array.isArray(tags) ? tags.map(String) : [];
  } catch {
    return [];
  }
}

export function requiresFeedbackDiscussion(): boolean {
  return Boolean(
    app.forum.attribute('huseyinfiliz.traderfeedback.requireDiscussion') ||
    app.forum.attribute('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked') ||
    allowedFeedbackTags().length
  );
}

export function allowsFeedback(discussion: Discussion | undefined): boolean {
  if (!discussion) return false;

  const tags = allowedFeedbackTags();
  if (tags.length && !(discussion as any).tags?.()?.some((tag: any) => tag && tags.includes(tag.id()))) return false;

  return !app.forum.attribute('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked') || Boolean((discussion as any).isLocked?.());
}
