import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import toast, { Toaster } from "react-hot-toast";

import "./styles/app.css";
import "./styles/mobile.css";

import Home from "./pages/Home";
import Dashboard from "./pages/Dashboard";
import Admin from "./pages/Admin";
import ClassRoom from "./pages/ClassRoom";
import Page404 from "./pages/Page404";
import Page500 from "./pages/Page500";

import Login from "./forms/Login";
import UserUpdate from "./forms/UserUpdate";
import UserCreate from "./forms/UserCreate";

import ClassRoomUpdate from "./forms/ClassRoomUpdate";
import ClassRoomCreate from "./forms/ClassRoomCreate";

const root = ReactDOM.createRoot(document.getElementById("root"));

root.render(
  <BrowserRouter>
    <Routes>
      <Route path="/" element={<Home />} />
      <Route path="/dashboard" element={<Dashboard />} />
      <Route path="/admin" element={<Admin />} />
      <Route path="/classroom/:classRoom" element={<ClassRoom />} />
      <Route path="/login" element={<Login />} />
      <Route path="/user/new" element={<UserCreate />} />
      <Route path="/user/:user/update" element={<UserUpdate />} />
      <Route path="/classroom/new" element={<ClassRoomCreate />} />
      <Route path="/error" element={<Page500 />} />
      <Route
        path="/classroom/:classRoom/update"
        element={<ClassRoomUpdate />}
      />
      <Route path="*" element={<Page404 />} />
    </Routes>
    <Toaster />
  </BrowserRouter>
);
