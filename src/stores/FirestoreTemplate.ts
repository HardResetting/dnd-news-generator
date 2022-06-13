import { FirebaseTemplate, Template } from "@/typings/Globals";
import {
  addDoc,
  deleteDoc,
  doc,
  type DocumentChange,
  DocumentReference,
  onSnapshot,
  query,
} from "@firebase/firestore";
import { collection, getDocs } from "firebase/firestore";
import { db } from "./FirestoreDb";

const tableName = "templates";

export type callbackObject = {
  removed: ((changes: DocumentChange<FirebaseTemplate>) => void) | undefined;
  added: ((changes: DocumentChange<FirebaseTemplate>) => void) | undefined;
  modified: ((changes: DocumentChange<FirebaseTemplate>) => void) | undefined;
};

export async function loadInitialData(): Promise<FirebaseTemplate[]> {
  const q = query(
    collection(db, tableName).withConverter(FirebaseTemplate.converter)
  );

  const querySnapshot = await getDocs(q);
  return querySnapshot.docs.map((doc) => doc.data());
}

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

export async function deleteTemplate(key: string): Promise<boolean> {
  try {
    await deleteDoc(doc(db, tableName, key));
    return true;
  }
  catch (e) {
    return false;
  }
}
