import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import CommentPost from 'flarum/forum/components/CommentPost';
import Icon from 'flarum/common/components/Icon';

export default function addPostBadge() {
  extend(CommentPost.prototype, 'headerItems', function (items) {
    // Ayar kapalıysa gösterme
    if (!app.forum.attribute('huseyinfiliz.traderfeedback.showBadgeInPosts')) {
      return;
    }

    const post = this.attrs.post;
    const user = post.user();

    if (!user || !user.traderStats()) return;

    const stats = user.traderStats();
    const total = stats.positiveCount() + stats.neutralCount() + stats.negativeCount();

    // En az 1 feedback olmalı
    if (total === 0) return;

    // Only first post kontrolü
    const onlyFirstPost = app.forum.attribute('huseyinfiliz.traderfeedback.badgeOnlyFirstPost');
    if (onlyFirstPost && post.number() !== 1) {
      return;
    }

    // Tag filtering kontrolü
    const tagFilterJson = app.forum.attribute('huseyinfiliz.traderfeedback.badgeTagFilter') || '[]';
    let allowedTags = [];

    try {
      allowedTags = JSON.parse(tagFilterJson);
    } catch (e) {
      allowedTags = [];
    }

    // Eğer tag filter varsa, kontrolü yap
    if (allowedTags.length > 0) {
      const discussion = post.discussion();

      if (!discussion) return;

      const discussionTags = discussion.tags ? discussion.tags() : [];

      if (!discussionTags || discussionTags.length === 0) {
        return; // Discussion'ın tag'i yoksa badge gösterme
      }

      // Discussion'ın tag'lerinden en az biri allowed tags içinde olmalı
      const hasAllowedTag = discussionTags.some((tag) => allowedTags.includes(tag.id()));

      if (!hasAllowedTag) {
        return; // İzin verilen tag yoksa badge gösterme
      }
    }

    // Badge text'ini oluştur
    const badgeText = getBadgeText(stats);
    const customPrefix = app.forum.attribute('huseyinfiliz.traderfeedback.badgeCustomPrefix') || '';

    items.add(
      'traderBadge',
      <span className="TraderBadge TraderBadge--inline">
        <Icon name="fas fa-cart-shopping" />
        {customPrefix && <span className="TraderBadge-prefix">{customPrefix}</span>}
        <span className="TraderBadge-score">{badgeText}</span>
      </span>,
      0
    );
  });
}

/**
 * Badge text'ini format'a göre oluşturur
 */
function getBadgeText(stats) {
  const format = app.forum.attribute('huseyinfiliz.traderfeedback.badgeFormat') || 'percentage';

  const total = stats.positiveCount() + stats.neutralCount() + stats.negativeCount();
  const score = Math.round(stats.score());
  const positive = stats.positiveCount();
  const neutral = stats.neutralCount();
  const negative = stats.negativeCount();

  switch (format) {
    case 'percentage':
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.badge.format_percentage', {
        score: score,
      });

    case 'count_percentage':
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.badge.format_count_percentage', {
        total: total,
        score: score,
      });

    case 'letters':
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.badge.format_letters', {
        positive: positive,
        neutral: neutral,
        negative: negative,
      });

    case 'symbols':
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.badge.format_symbols', {
        positive: positive,
        neutral: neutral,
        negative: negative,
      });

    case 'custom':
      const customTemplate =
        app.forum.attribute('huseyinfiliz.traderfeedback.badgeCustomFormat') || '{total} ({score}%) - {positive}P / {neutral}N / {negative}N';
      return customTemplate
        .replace(/\{total\}/g, total)
        .replace(/\{score\}/g, score)
        .replace(/\{positive\}/g, positive)
        .replace(/\{neutral\}/g, neutral)
        .replace(/\{negative\}/g, negative);

    default:
      return `${score}%`;
  }
}
