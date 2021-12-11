import { FirebaseTemplateItem, TemplateItem } from "@/typings/Globals";
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

const tableName = "templateItems";

export type callbackObject = {
  removed:
    | ((changes: DocumentChange<FirebaseTemplateItem>) => void)
    | undefined;
  added: ((changes: DocumentChange<FirebaseTemplateItem>) => void) | undefined;
  modified:
    | ((changes: DocumentChange<FirebaseTemplateItem>) => void)
    | undefined;
};

export function registerToSnapshot(callbackObject?: callbackObject): void {
  const q = query(collection(db, tableName)).withConverter(
    FirebaseTemplateItem.converter
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

export async function addTemplateItem(
  newItem: TemplateItem
): Promise<DocumentReference<TemplateItem>> {
  const ref = collection(db, tableName).withConverter(
    FirebaseTemplateItem.converter
  );

  const doc = await addDoc(ref, newItem);
  return doc;
}

export function deleteTemplateItem(key: string): Promise<void> {
  return deleteDoc(doc(db, tableName, key));
}

// querySnapshot.docChanges().forEach((changes) => {
//     switch (changes.type) {
//       case "removed":
//         this.FirebaseItems = this.FirebaseItems.filter(
//           (obj) => obj.key != changes.doc.id
//         );
//         break;

//       case "added":
//         this.FirebaseItems.push(changes.doc.data());
//         break;

//       case "modified": {
//         var index = this.FirebaseItems.findIndex(
//           (obj) => obj.key == changes.doc.id
//         );

//         if (~index) {
//           this.FirebaseItems[index] = changes.doc.data();
//         }
//       }
//     }
//   });
