import type {
  DocumentData,
  QueryDocumentSnapshot,
  SnapshotOptions,
} from "firebase/firestore";

export interface IPlainObject {
  key: string;
  value: string;
}

export class TemplateItem {
  singular: string;
  plural: string;
  type: string;

  constructor(singular = "", plural = "", type = "") {
    this.singular = singular;
    this.plural = plural;
    this.type = type;
  }
}

export class FirebaseTemplateItem extends TemplateItem {
  key: string;

  constructor(key: string, singular: string, plural: string, type: string) {
    super(singular, plural, type);
    this.key = key;
  }

  static converter = {
    toFirestore(FirebaseItem: FirebaseTemplateItem): DocumentData {
      return {
        key: FirebaseItem.key,
        singular: FirebaseItem.singular,
        plural: FirebaseItem.plural,
        type: FirebaseItem.type,
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
        data.type
      );
    },
  };
}


export class Template {
  value: string;

  constructor(value = "") {
    this.value = value;
  }
}

export class FirebaseTemplate extends Template {
  key: string;

  constructor(key: string, value: string) {
    super(value);
    this.key = key;
  }

  static converter = {
    toFirestore(FirebaseTemplate: FirebaseTemplate): DocumentData {
      return {
        value: FirebaseTemplate.value,
      };
    },
    fromFirestore(
      snapshot: QueryDocumentSnapshot,
      options: SnapshotOptions
    ): FirebaseTemplate {
      const data = snapshot.data(options);

      return new FirebaseTemplate(snapshot.id, data.value);
    },
  };
}
