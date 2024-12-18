import React, { Component, useState, useEffect } from "react";
import { Route, Switch, Redirect, withRouter } from "react-router-dom";
import toast, { Toaster } from "react-hot-toast";

import AvoAxios from "../components/AvoAxios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import Loader from "../components/Loader";

import Select from "react-select";

export default function UserCreate() {
  const [userName, setUserName] = useState("");
  const [userPass, setUserPass] = useState("");
  const [userRole, setUserRole] = useState(null);

  const roleOptions = [
    { value: "0", label: "Teacher" },
    { value: "1", label: "Admin" },
  ];

  React.useEffect(() => {
    const listener = (event) => {
      if (event.code === "Enter" || event.code === "NumpadEnter") {
        event.preventDefault();
        document.getElementById("form-submit").click();
      }
    };
    document.addEventListener("keydown", listener);
    return () => {
      document.removeEventListener("keydown", listener);
    };
  }, []);

  const handleName = (e) => {
    const { name, value } = e.target;
    setUserName((prevUserName) => value);
  };

  const handlePass = (e) => {
    const { name, value } = e.target;
    setUserPass((prevUserPass) => value);
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (userName === "" || userName.length < 3) {
      toast.error("The name must be 4 characters or longer");
      return;
    }

    if (userPass === "" || userPass.length < 4) {
      toast.error("The password must be 4 characters or longer");
      return;
    }

    var admin = userRole.value ? 1 : 0; // Ensures we return an integer

    console.log(userName);
    console.log(userPass);
    console.log(admin);
    return;

    AvoAxios.post("users/update", {
      name: userName,
      password: userPass,
      admin: admin,
    }).then((res) => {
      sessionStorage.setItem("toastMessage", "New user created!");
      window.location.href = "/dashboard";
    });
  };

  return (
    <>
      <Header />
      <div className="avo-content form-page">
        <div>
          <form className={"avo-form"} id="avo-user-create">
            <div className="avo-page-title">Create new user</div>
            <div className="avo-field field-input">
              <label>Username: </label>
              <input
                type="text"
                id="username"
                name="username"
                required
                minLength="3"
                maxLength="32"
                value={userName}
                onChange={handleName}
              />
            </div>
            <div className="avo-field field-input field-password">
              <label>Password: </label>
              <input
                type="password"
                id="password"
                required
                minLength="4"
                maxLength="32"
                onChange={handlePass}
              />
            </div>
            <div className="avo-field field-select">
              <label>Role: </label>
              <Select
                options={roleOptions}
                id="role"
                name="role"
                required
                onChange={(newValue) => setUserRole(newValue)}
              />
            </div>
            <div className="avo-form-actions">
              <input
                type="button"
                onClick={handleSubmit}
                value={"Create User"}
                id="form-submit"
              />
            </div>
          </form>
        </div>
        <Footer />
      </div>
    </>
  );
}
