import type {
  DocumentData,
  QueryDocumentSnapshot,
  SnapshotOptions,
} from "firebase/firestore";

export class PlainObject {
  value: string;

  constructor(value = "") {
    this.value = value;
  }
}

export class TemplateItem {
  singular: string;
  plural: string;
  type: string;
  timestamp: Date;

  constructor(singular = "", plural = "", type = "", timestamp = new Date()) {
    this.singular = singular;
    this.plural = plural;
    this.type = type;
    this.timestamp = timestamp;
  }
}

export class FirebaseTemplateItem extends TemplateItem {
  key: string;

  constructor(
    key: string,
    singular: string,
    plural: string,
    type: string,
    timestamp: Date
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
  timestamp: Date;

  constructor(value = "", timestamp = new Date()) {
    this.value = value;
    this.timestamp = timestamp;
  }
}

export class FirebaseTemplate extends Template {
  key: string;

  constructor(key: string, value: string, timestamp: Date) {
    super(value, timestamp);
    this.key = key;
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
