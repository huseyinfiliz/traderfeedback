---
name: flarum2-avatar
description: >-
  Best practices, architecture, and standards for user avatars in Flarum 2.x extensions and core.
  Use when styling, resizing, rendering, or troubleshooting avatars, profile pictures, or `.Avatar` in Flarum 2.x.
---

# Flarum 2.x Avatar Architecture & Styling Guide

This skill documents how Flarum 2.x handles user avatars in both the frontend (Mithril/TypeScript) and stylesheets (LESS), explains why initial-letter avatars overflow when resized improperly, and details the core-standard solution.

---

## 1. Core Architecture (`vendor/flarum/core/less/common/Avatar.less`)

In Flarum 2.x, `.Avatar` is styled around a CSS Custom Property `--size`:

```less
.Avatar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-sizing: content-box;
  color: #fff;
  vertical-align: top;
  background-color: var(--avatar-bg);
  font-weight: normal;
  .Avatar--size(48px); /* Default size */
  width: var(--size);
  height: var(--size);
  border-radius: 100%;
  font-size: calc(~"var(--size) / 2");

  img {
    display: inline-block;
    width: 100%;
    height: 100%;
    border-radius: 100%;
    vertical-align: top;
  }
}

.Avatar--size(@size) {
  --size: @size;
}
```

Notice that:
- Avatar `width` and `height` are bound to `var(--size)`.
- The avatar initial letter's font size is dynamically computed: `font-size: calc(~"var(--size) / 2");`.
- When an avatar is 48px, the font size is 24px.

---

## 2. The Pitfall: Why Letters Do Not Scale Down

### What Happens When You Override `width` / `height` Directly:
```less
/* INCORRECT */
.MyComponent-avatar {
  width: 28px;
  height: 28px;
}
```
- The CSS property `--size` is **not** updated and remains `48px`.
- The container shrinks to `28px x 28px`.
- However, `font-size` remains `calc(48px / 2) = 24px`!
- The username initial letter remains 24px, completely overflowing the 28px circle.

### What Happens When You Use `font-size: 14px !important;`:
- This is an anti-pattern.
- It breaks responsive avatar variations.
- It hardcodes a font size regardless of whether the avatar is 20px, 32px, 48px, or 64px.
- It causes specificity conflicts and breaks core components.

---

## 3. The Core Solution: `.Avatar--size(@size)`

Whenever you need to size an avatar in LESS, **always** call the core mixin `.Avatar--size(@size)`:

```less
/* CORRECT */
.MyComponent-avatar {
  .Avatar--size(28px);
}
```

### Why This Works:
- `.Avatar--size(28px)` compiles to `--size: 28px;`.
- `width` becomes `28px`.
- `height` becomes `28px`.
- `font-size` becomes `calc(28px / 2) = 14px`.
- The letter scales down proportionally and stays perfectly centered!

---

## 4. Sizing Standards in Flarum Core

Flarum core uses `.Avatar--size(@size)` across all components:

| Component | Core File | Mixin Call | Resulting Font Size |
| :--- | :--- | :--- | :--- |
| Header / Buttons | `common/Button.less`, `forum/HeaderList.less` | `.Avatar--size(24px);` | 12px |
| Post Small / Discussion List | `forum/DiscussionListItem.less`, `forum/Post.less` | `.Avatar--size(32px);` | 16px |
| Discussion List Alternate | `forum/DiscussionListItem.less` | `.Avatar--size(36px);` | 18px |
| Default Avatar | `common/Avatar.less` | `.Avatar--size(48px);` | 24px |
| Composer / Post Large | `forum/Composer.less`, `forum/Post.less` | `.Avatar--size(64px);` | 32px |
| User Card Profile | `forum/UserCard.less` | `.Avatar--size(96px);` | 48px |

---

## 5. Frontend Component Usage (Mithril / TypeScript)

### Standard Usage with Existing User Model:
Use the standard Flarum component `Avatar`:

```tsx
import Avatar from 'flarum/common/components/Avatar';

// Standard render:
<Avatar user={user} className="MyComponent-avatar" />
```

`Avatar` automatically:
1. Renders an `<img src={user.avatarUrl()} className="Avatar MyComponent-avatar" />` if the user has an uploaded avatar.
2. Renders a `<span role="img" aria-label={username} className="Avatar MyComponent-avatar" style={{ '--avatar-bg': user.color() }}>{initial}</span>` if no avatar is uploaded.

### Handling Deleted / Anonymous / Fallback Users:
When rendering an avatar for a deleted or null user:

```tsx
// Option A: Core Avatar with null user
<Avatar user={null} className="MyComponent-avatar" />

// Option B: Custom fallback placeholder matching core semantics
<span
  className="Avatar MyComponent-avatar"
  role="img"
  aria-label={deletedUsername}
  style={{ '--avatar-bg': '#7f8c8d' }}
>
  {deletedUsername.charAt(0).toUpperCase() || '?'}
</span>
```
Ensure that custom fallbacks:
- Include the `Avatar` CSS class.
- Pass `--avatar-bg` via style for background coloring.
- Avoid inline font sizes or hardcoded pixel heights.

---

## 6. Build Instructions
- Always compile frontend assets with **`yarn build`**.
- Never use `npm`.
```bash
cd js && yarn build
php flarum cache:clear
```
