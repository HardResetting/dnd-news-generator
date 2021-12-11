import {
  FirebaseTemplate,
  FirebaseTemplateItem,
  Template,
  TemplateItem,
} from "@/typings/Globals";
import { DocumentChange } from "firebase/firestore";
import {
  createStore,
  Store as VuexStore,
  MutationTree,
  ActionContext,
  ActionTree,
  GetterTree,
  CommitOptions,
  DispatchOptions,
  createLogger,
} from "vuex";
import {
  addTemplate,
  deleteTemplate,
  registerToTemplateSnapshot,
} from "./FirestoreTemplate";
import {
  addTemplateItem,
  deleteTemplateItem,
  registerToTemplateItemSnapshot,
} from "./FirestoreTemplateItem";

//declare state
export type State = {
  count: number;
  dataLoaded: boolean | null;
  FirebaseTemplateItems: Array<FirebaseTemplateItem>;
  FirebaseTemplates: Array<FirebaseTemplate>;
};

//set state
const state: State = {
  count: 0,
  dataLoaded: null,
  FirebaseTemplateItems: new Array<FirebaseTemplateItem>(),
  FirebaseTemplates: new Array<FirebaseTemplate>(),
};

// mutations and action enums
export enum MutationTypes {
  ADD_FIREBASE_TEMPLATE_ITEM = "ADD_FIREBASE_TEMPLATE_ITEM",
  DELETE_FIREBASE_TEMPLATE_ITEM = "DELETE_FIREBASE_TEMPLATE_ITEM",
  MODIFY_FIREBASE_TEMPLATE_ITEM = "MODIFY_FIREBASE_TEMPLATE_ITEM",
  ADD_FIREBASE_TEMPLATE = "ADD_FIREBASE_TEMPLATE",
  DELETE_FIREBASE_TEMPLATE = "DELETE_FIREBASE_TEMPLATE",
  MODIFY_FIREBASE_TEMPLATE = "MODIFY_FIREBASE_TEMPLATE",
}

export enum ActionTypes {
  DATABASE_INIT_DATA_TEMPLATE_ITEMS = "DATABASE_INIT_DATA_TEMPLATE_ITEMS",
  DATABASE_ADD_FIREBASE_TEMPLATE_ITEM = "DATABASE_ADD_FIREBASE_TEMPLATE_ITEM",
  DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM = "DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM",
  DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE = "DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE",
  DATABASE_MODIFY_FIREBASE_TEMPLATE_ITEM = "DATABASE_MODIFY_FIREBASE_TEMPLATE_ITEM",
  DATABASE_INIT_DATA_TEMPLATES = "DATABASE_INIT_DATA_TEMPLATES",
  DATABASE_ADD_FIREBASE_TEMPLATE = "DATABASE_ADD_FIREBASE_TEMPLATE",
  DATABASE_DELETE_FIREBASE_TEMPLATE = "DATABASE_DELETE_FIREBASE_TEMPLATE",
  DATABASE_MODIFY_FIREBASE_TEMPLATE = "DATABASE_MODIFY_FIREBASE_TEMPLATE",
}

// mutation types
export type Mutations<S = State> = {
  [MutationTypes.ADD_FIREBASE_TEMPLATE_ITEM](
    state: S,
    item: FirebaseTemplateItem
  ): void;
  [MutationTypes.DELETE_FIREBASE_TEMPLATE_ITEM](state: S, key: string): void;
  [MutationTypes.MODIFY_FIREBASE_TEMPLATE_ITEM](
    state: S,
    changes: DocumentChange<FirebaseTemplateItem>
  ): void;
  [MutationTypes.ADD_FIREBASE_TEMPLATE](state: S, item: FirebaseTemplate): void;
  [MutationTypes.DELETE_FIREBASE_TEMPLATE](state: S, key: string): void;
  [MutationTypes.MODIFY_FIREBASE_TEMPLATE](
    state: S,
    changes: DocumentChange<FirebaseTemplate>
  ): void;
};

// define mutations
const mutations: MutationTree<State> & Mutations = {
  [MutationTypes.ADD_FIREBASE_TEMPLATE_ITEM](
    state: State,
    firebaseTemplateItem: FirebaseTemplateItem
  ) {
    state.FirebaseTemplateItems.push(firebaseTemplateItem);
  },
  [MutationTypes.DELETE_FIREBASE_TEMPLATE_ITEM](state: State, key: string) {
    state.FirebaseTemplateItems = state.FirebaseTemplateItems.filter(
      (obj) => obj.key != key
    );
  },
  [MutationTypes.MODIFY_FIREBASE_TEMPLATE_ITEM](
    state: State,
    changes: DocumentChange<FirebaseTemplateItem>
  ) {
    const index = state.FirebaseTemplateItems.findIndex(
      (obj) => obj.key == changes.doc.id
    );

    if (~index) {
      state.FirebaseTemplateItems[index] = changes.doc.data();
    }
  },
  [MutationTypes.ADD_FIREBASE_TEMPLATE](
    state: State,
    firebaseTemplate: FirebaseTemplate
  ) {
    state.FirebaseTemplates.push(firebaseTemplate);
  },
  [MutationTypes.DELETE_FIREBASE_TEMPLATE](state: State, key: string) {
    state.FirebaseTemplates = state.FirebaseTemplates.filter(
      (obj) => obj.key != key
    );
  },
  [MutationTypes.MODIFY_FIREBASE_TEMPLATE](
    state: State,
    changes: DocumentChange<FirebaseTemplate>
  ) {
    const index = state.FirebaseTemplates.findIndex(
      (obj) => obj.key == changes.doc.id
    );

    if (~index) {
      state.FirebaseTemplates[index] = changes.doc.data();
    }
  },
};

//actions
type AugmentedActionContext = {
  commit<K extends keyof Mutations>(
    key: K,
    payload: Parameters<Mutations[K]>[1]
  ): ReturnType<Mutations[K]>;
} & Omit<ActionContext<State, State>, "commit">;

// actions interface

export interface Actions {
  [ActionTypes.DATABASE_INIT_DATA_TEMPLATE_ITEMS]({
    commit,
  }: AugmentedActionContext): void;
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM](
    { commit }: AugmentedActionContext,
    key: string
  ): void;
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE](
    { commit }: AugmentedActionContext,
    type: string
  ): void;
  [ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM](
    { commit }: AugmentedActionContext,
    templateItem: TemplateItem
  ): void;
  [ActionTypes.DATABASE_INIT_DATA_TEMPLATES]({ commit }: AugmentedActionContext,
  ): void;
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE](
    { commit }: AugmentedActionContext,
    key: string
  ): void;
  [ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE](
    { commit }: AugmentedActionContext,
    template: Template
  ): void;
}

export const actions: ActionTree<State, State> & Actions = {
  [ActionTypes.DATABASE_INIT_DATA_TEMPLATE_ITEMS]({ commit }) {
    registerToTemplateItemSnapshot({
      removed: (changes: DocumentChange<FirebaseTemplateItem>) => {
        commit(MutationTypes.DELETE_FIREBASE_TEMPLATE_ITEM, changes.doc.id);
      },
      added: (changes: DocumentChange<FirebaseTemplateItem>) => {
        commit(MutationTypes.ADD_FIREBASE_TEMPLATE_ITEM, changes.doc.data());
      },
      modified: (changes: DocumentChange<FirebaseTemplateItem>) => {
        commit(MutationTypes.MODIFY_FIREBASE_TEMPLATE_ITEM, changes);
      },
    });
    state.dataLoaded = true;
  },
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM]({ commit }, key) {
    deleteTemplateItem(key);
  },
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE]({ commit }, type) {
    state.FirebaseTemplateItems.forEach(item => {
      if (item.type == type) {
        deleteTemplateItem(item.key);
      }
    })
  },
  [ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM]({ commit }, templateItem) {
    addTemplateItem(templateItem);
  },
  [ActionTypes.DATABASE_MODIFY_FIREBASE_TEMPLATE_ITEM](
    { commit },
    firebaseTemplateItem
  ) {
    console.log(firebaseTemplateItem);
  },
  [ActionTypes.DATABASE_INIT_DATA_TEMPLATES]({ commit }) {
    registerToTemplateSnapshot({
      removed: (changes: DocumentChange<FirebaseTemplate>) => {
        commit(MutationTypes.DELETE_FIREBASE_TEMPLATE, changes.doc.id);
      },
      added: (changes: DocumentChange<FirebaseTemplate>) => {
        commit(MutationTypes.ADD_FIREBASE_TEMPLATE, changes.doc.data());
      },
      modified: (changes: DocumentChange<FirebaseTemplate>) => {
        commit(MutationTypes.MODIFY_FIREBASE_TEMPLATE, changes);
      },
    });
  },
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE]({ commit }, key) {
    deleteTemplate(key);
  },
  [ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE]({ commit }, template) {
    addTemplate(template);
  },
  [ActionTypes.DATABASE_MODIFY_FIREBASE_TEMPLATE](
    { commit },
    firebaseTemplate
  ) {
    console.log(firebaseTemplate);
  },
};

// Getters types
export type Getters = {
  firebaseTemplateItemTypes(state: State): string[];
};

//getters

export const getters: GetterTree<State, State> & Getters = {
  firebaseTemplateItemTypes: (): string[] => {
    return state.FirebaseTemplateItems
      .map(function (item) { return item.type; })
      .filter((value, index, self) => {
        return self.indexOf(value) === index
      });
    ;
  },
};

//setup store type
export type Store = Omit<
  VuexStore<State>,
  "commit" | "getters" | "dispatch"
> & {
  commit<K extends keyof Mutations, P extends Parameters<Mutations[K]>[1]>(
    key: K,
    payload: P,
    options?: CommitOptions
  ): ReturnType<Mutations[K]>;
} & {
  getters: {
    [K in keyof Getters]: ReturnType<Getters[K]>;
  };
} & {
  dispatch<K extends keyof Actions>(
    key: K,
    payload: Parameters<Actions[K]>[1],
    options?: DispatchOptions
  ): ReturnType<Actions[K]>;
};

export const store = createStore({
  state,
  mutations,
  actions,
  getters,
  plugins: [createLogger()],
});

export function useStore(): Store {
  return store as Store;
}
