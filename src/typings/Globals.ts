import type {
  DocumentData,
  QueryDocumentSnapshot,
  SnapshotOptions,
} from "firebase/firestore";
import { Timestamp } from "firebase/firestore";

export class TemplateItem {
  singular: string;
  plural: string;
  type: string;
  timestamp: Timestamp;

  constructor(
    singular = "",
    plural = "",
    type = "",
    timestamp = Timestamp.now()
  ) {
    this.singular = singular;
    this.plural = plural;
    this.type = type;
    this.timestamp = timestamp;
  }

  public static isTemplateItem(value: unknown): value is TemplateItem {
    const fti = value as TemplateItem;
    return (
      fti.plural !== undefined &&
      fti.singular !== undefined &&
      fti.timestamp !== undefined &&
      fti.type !== undefined
    );
  }
}

export class FirebaseTemplateItem extends TemplateItem {
  key: string;

  constructor(
    key: string,
    singular: string,
    plural: string,
    type: string,
    timestamp: Timestamp
  ) {
    super(singular, plural, type, timestamp);
    this.key = key;
  }

  equals(other: FirebaseTemplateItem | undefined): boolean {
    if (other === undefined) return false;

    return (
      this.singular === other.singular &&
      this.plural === other.plural &&
      this.type === other.type &&
      this.timestamp === other.timestamp
    );
  }

  public static isFirebaseTemplateItem(
    value: unknown
  ): value is FirebaseTemplateItem {
    const fti = value as FirebaseTemplateItem;
    return TemplateItem.isTemplateItem(value) && fti.key !== undefined;
  }

  static converter = {
    toFirestore(FirebaseItem: FirebaseTemplateItem): DocumentData {
      return {
        singular: FirebaseItem.singular,
        plural: FirebaseItem.plural,
        type: FirebaseItem.type,
        timestamp: FirebaseItem.timestamp,
      };
    },
    fromFirestore(
      snapshot: QueryDocumentSnapshot,
      options: SnapshotOptions
    ): FirebaseTemplateItem {
      const data = snapshot.data(options);

      return new FirebaseTemplateItem(
        snapshot.id,
        data.singular,
        data.plural,
        data.type,
        data.timestamp
      );
    },
  };
}

export class Template {
  value: string;
  timestamp: Timestamp;

  constructor(value = "", timestamp = Timestamp.now()) {
    this.value = value;
    this.timestamp = timestamp;
  }

  public static isTemplate(value: unknown): value is Template {
    const t = value as Template;
    return t.value !== undefined && t.timestamp !== undefined;
  }
}

export class FirebaseTemplate extends Template {
  key: string;

  constructor(key: string, value: string, timestamp: Timestamp) {
    super(value, timestamp);
    this.key = key;
  }

  public static isFirebaseTemplate(value: unknown): value is FirebaseTemplate {
    if (!Template.isTemplate(value)) return false;
    const ft = value as FirebaseTemplate;
    return ft.key !== undefined;
  }

  static converter = {
    toFirestore(FirebaseTemplate: FirebaseTemplate): DocumentData {
      return {
        value: FirebaseTemplate.value,
        timestamp: FirebaseTemplate.timestamp,
      };
    },
    fromFirestore(
      snapshot: QueryDocumentSnapshot,
      options: SnapshotOptions
    ): FirebaseTemplate {
      const data = snapshot.data(options);

      return new FirebaseTemplate(snapshot.id, data.value, data.timestamp);
    },
  };
}
