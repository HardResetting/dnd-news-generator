import { FirebaseTemplateItem, TemplateItem } from "@/typings/Globals";
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
import { addTemplateItem, deleteTemplateItem, registerToSnapshot } from "./FirestoreTemplateItem";

//declare state
export type State = {
  count: number;
  dataLoaded: boolean | null;
  FirebaseTemplateItems: Array<FirebaseTemplateItem>;
};

//set state
const state: State = {
  count: 0,
  dataLoaded: null,
  FirebaseTemplateItems: new Array<FirebaseTemplateItem>(),
};

// mutations and action enums
export enum MutationTypes {
  ADD_FIREBASE_TEMPLATE_ITEM = "ADD_FIREBASE_TEMPLATE_ITEM",
  DELETE_FIREBASE_TEMPLATE_ITEM = "DELETE_FIREBASE_TEMPLATE_ITEM",
  MODIFY_FIREBASE_TEMPLATE_ITEM = "MODIFY_FIREBASE_TEMPLATE_ITEM",
}

export enum ActionTypes {
  DATABASE_INIT_DATA = "DATABASE_INIT_DATA",
  DATABASE_ADD_FIREBASE_TEMPLATE_ITEM = "DATABASE_ADD_FIREBASE_TEMPLATE_ITEM",
  DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM = "DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM",
  DATABASE_MODIFY_FIREBASE_TEMPLATE_ITEM = "DATABASE_MODIFY_FIREBASE_TEMPLATE_ITEM",
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
  [ActionTypes.DATABASE_INIT_DATA]({ commit }: AugmentedActionContext): void;
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM](
    { commit }: AugmentedActionContext,
    key: string
  ): void;
  [ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM](
    { commit }: AugmentedActionContext,
    templateItem: TemplateItem
  ): void;
}

export const actions: ActionTree<State, State> & Actions = {
  [ActionTypes.DATABASE_INIT_DATA]({ commit }) {
    registerToSnapshot({
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
  },
  [ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM]({ commit }, key) {
    deleteTemplateItem(key);
  },
  [ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM]({ commit }, templateItem) {
    addTemplateItem(templateItem);
  },
  [ActionTypes.DATABASE_MODIFY_FIREBASE_TEMPLATE_ITEM]({ commit }, firebaseTemplateItem) {
    console.log(firebaseTemplateItem);
  },
};

// Getters types
export type Getters = {
  firebaseTemplateItems(state: State): FirebaseTemplateItem[];
};

//getters

export const getters: GetterTree<State, State> & Getters = {
  firebaseTemplateItems: (state): FirebaseTemplateItem[] => {
    return state.FirebaseTemplateItems;
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
