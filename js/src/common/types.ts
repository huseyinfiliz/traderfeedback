export type FeedbackType = 'positive' | 'neutral' | 'negative';
export type FeedbackRole = 'buyer' | 'seller' | 'trader';

export interface FeedbackAttributes {
  type: FeedbackType;
  role: FeedbackRole;
  comment: string;
  isApproved: boolean;
  fromUserId: number;
  toUserId: number;
  discussionId: number | null;
  createdAt: string;
  updatedAt: string;
  canEdit: boolean;
  canDelete: boolean;
  canReport: boolean;
  canModerate: boolean;
  discussionExists: boolean;
  canViewDiscussion: boolean;
}

export interface ApiResource<Attributes = Record<string, unknown>> {
  type: string;
  id: string;
  attributes: Attributes;
  relationships?: Record<string, { data: { type: string; id: string } | null } | null>;
}

export type FeedbackData = ApiResource<FeedbackAttributes>;

export interface ProfileStats {
  score: number;
  positive_count: number;
  neutral_count: number;
  negative_count: number;
}
