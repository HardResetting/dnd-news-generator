// Initialize Cloud Firestore through Firebase
import { initializeApp } from "firebase/app";
import { getFirestore } from "firebase/firestore";
initializeApp({
  apiKey: "AIzaSyBF-Ifyk6cweB9ctDteUfEiorWSmTvP8bc",
  authDomain: "dnd-news-generator.firebaseapp.com",
  projectId: "dnd-news-generator",
  storageBucket: "dnd-news-generator.appspot.com",
  messagingSenderId: "641861845147",
  appId: "1:641861845147:web:35ac6af139a3608050063e",
});

export const db = getFirestore();
