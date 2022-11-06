import type {
  FirebaseTemplate,
  FirebaseTemplateItem,
  Template,
  TemplateItem,
} from "@/typings/Globals";
import type { DocumentChange } from "firebase/firestore";
import { defineStore, mapActions } from "pinia";
import {
  addTemplate,
  deleteTemplate,
  editTemplate,
  loadInitialData as loadInitialTemplateData,
  registerToTemplateSnapshot,
} from "./FirestoreTemplate";
import {
  addTemplateItem,
  deleteTemplateItem,
  editTemplateItem,
  loadInitialTemplateItemData,
  registerToTemplateItemSnapshot,
} from "./FirestoreTemplateItem";

// useStore could be anything like useUser, useCart
// the first argument is a unique id of the store across your application
export const useStore = defineStore("main", {
  state: () => ({
    FirebaseTemplateItems: new Array<FirebaseTemplateItem>(),
    FirebaseTemplates: new Array<FirebaseTemplate>(),
    isFirebaseTemplatesLoading: true,
    isFirebaseTemplateItemsLoading: true,
  }),
  actions: {
    async DATABASE_INIT_DATA_TEMPLATE_ITEMS() {
      this.FirebaseTemplateItems = await loadInitialTemplateItemData();
      this.isFirebaseTemplateItemsLoading = false;
      registerToTemplateItemSnapshot({
        removed: (changes: DocumentChange<FirebaseTemplateItem>) => {
          this.FirebaseTemplateItems = this.FirebaseTemplateItems.filter(
            (e) => e.key != changes.doc.data().key
          );
        },
        added: (changes: DocumentChange<FirebaseTemplateItem>) => {
          if (
            !this.FirebaseTemplateItems.some(
              (e) => e.key === changes.doc.data().key
            )
          )
            this.FirebaseTemplateItems.push(changes.doc.data());
        },
        modified: (changes: DocumentChange<FirebaseTemplateItem>) => {
          const index = this.FirebaseTemplateItems.findIndex(
            (obj) => obj.key == changes.doc.id
          );

          if (~index) {
            this.FirebaseTemplateItems[index] = changes.doc.data();
          }
        },
      });
    },
    async DATABASE_INIT_DATA_TEMPLATES() {
      this.FirebaseTemplates = await loadInitialTemplateData();
      this.isFirebaseTemplatesLoading = false;
      registerToTemplateSnapshot({
        removed: (changes: DocumentChange<FirebaseTemplate>) => {
          this.FirebaseTemplates = this.FirebaseTemplates.filter(
            (e) => e.key != changes.doc.data().key
          );
        },
        added: (changes: DocumentChange<FirebaseTemplate>) => {
          if (
            !this.FirebaseTemplates.some(
              (e) => e.key === changes.doc.data().key
            )
          )
            this.FirebaseTemplates.push(changes.doc.data());
        },
        modified: (changes: DocumentChange<FirebaseTemplate>) => {
          const index = this.FirebaseTemplates.findIndex(
            (obj) => obj.key == changes.doc.id
          );

          if (~index) {
            this.FirebaseTemplates[index] = changes.doc.data();
          }
        },
      });
    },
    async DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(key: string) {
      await deleteTemplateItem(key);
      this.FirebaseTemplateItems = this.FirebaseTemplateItems.filter(
        (e) => e.key != key
      );
    },
    async DATABASE_DELETE_FIREBASE_TEMPLATE(key: string) {
      const success = await deleteTemplate(key);
      console.log(success);

      if (success)
        this.FirebaseTemplates = this.FirebaseTemplates.filter(
          (e) => e.key != key
        );
    },
    async DATABASE_ADD_FIREBASE_TEMPLATE_ITEM(templateItem: TemplateItem) {
      return addTemplateItem(templateItem);
    },
    async DATABASE_ADD_FIREBASE_TEMPLATE(template: Template) {
      return addTemplate(template);
    },
    async DATABASE_UPDATE_FIREBASE_TEMPLATE_ITEM(
      key: string,
      newTemplateItem: FirebaseTemplateItem
    ) {
      return editTemplateItem(key, newTemplateItem)
    },
    async DATABASE_UPDATE_FIREBASE_TEMPLATE(key: string,
      newTemplate: string) {
      return editTemplate(key, newTemplate)
    },
  },
  getters: {
    isLoading: (state): boolean =>
      state.isFirebaseTemplatesLoading || state.isFirebaseTemplateItemsLoading,

    getFirebaseTemplateItemTypes: (state): Record<string, string>[] =>
      [...state.FirebaseTemplateItems]
        .flatMap(item => item.type.split(","))
        .map(item => item.trim())
        .filter(
          (value, index, self) => self.indexOf(value) === index
        )
        .reduce(
          (acc, cur) => {
            acc.push(cur);
            return acc;
          }, new Array<string>()).map(
            item => { return { "value": item } }
          )
    ,

    getFirebaseTemplateItemFilteredByTypes: (state): FirebaseTemplateItem[] =>
      [...state.FirebaseTemplateItems].filter(
        (value, index, self) => index === self.findIndex((fti) => fti.type === value.type)
      ),

    getFirebaseTemplateItem: state =>
      (id: string): FirebaseTemplateItem | undefined => state.FirebaseTemplateItems.find((e) => e.key == id),

    getFirebaseTemplate: state =>
      (id: string): FirebaseTemplate | undefined => state.FirebaseTemplates.find((e) => e.key == id),

    getRandomFirebaseTemplate: (state) => {
      return () => {
        const randomTemplate =
          state.FirebaseTemplates[
          Math.floor(Math.random() * state.FirebaseTemplates.length)
          ];
        return randomTemplate;
      };
    },
  },
});
