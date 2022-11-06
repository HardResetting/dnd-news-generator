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

  equals(other: FirebaseTemplateItem | undefined): boolean {
    if (other === undefined)
      return false;

    return this.key === other.key && this.singular === other.singular && this.plural === other.plural && this.type === other.type;
  }

  static converter = {
    toFirestore(FirebaseItem: FirebaseTemplateItem): DocumentData {
      return {
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
