import { FirebaseTemplate, Template } from "@/typings/Globals";
import {
  addDoc,
  deleteDoc,
  doc,
  DocumentChange,
  DocumentReference,
  onSnapshot,
  query,
} from "@firebase/firestore";
import { collection } from "firebase/firestore";
import { db } from "./FirestoreDb";

const tableName = "templates";

export type callbackObject = {
  removed: ((changes: DocumentChange<FirebaseTemplate>) => void) | undefined;
  added: ((changes: DocumentChange<FirebaseTemplate>) => void) | undefined;
  modified: ((changes: DocumentChange<FirebaseTemplate>) => void) | undefined;
};

export function registerToTemplateSnapshot(
  callbackObject?: callbackObject
): void {
  const q = query(collection(db, tableName)).withConverter(
    FirebaseTemplate.converter
  );

  onSnapshot(q, (querySnapshot) => {
    querySnapshot.docChanges().forEach((changes) => {
      if (callbackObject != undefined)
        switch (changes.type) {
          case "removed":
            callbackObject.removed?.call(undefined, changes);
            break;

          case "added":
            callbackObject.added?.call(undefined, changes);
            break;

          case "modified": {
            callbackObject.modified?.call(undefined, changes);
          }
        }
    });
  });
}

export async function addTemplate(
  newItem: Template
): Promise<DocumentReference<Template>> {
  const ref = collection(db, tableName).withConverter(
    FirebaseTemplate.converter
  );

  const doc = await addDoc(ref, newItem);
  return doc;
}

export function deleteTemplate(key: string): Promise<void> {
  return deleteDoc(doc(db, tableName, key));
}
