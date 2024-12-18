import React, { Component, useState, useEffect } from "react";
import {
  Route,
  Switch,
  Redirect,
  withRouter,
  useParams,
  useNavigate,
} from "react-router-dom";
import toast, { Toaster } from "react-hot-toast";

import AvoAxios from "../components/AvoAxios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import Loader from "../components/Loader";

import Select from "react-select";

export default function UserUpdate() {
  const navigate = useNavigate();
  const { user } = useParams();
  const [loading, setLoading] = useState(true);

  const [userName, setUserName] = useState("");
  const [userPass, setUserPass] = useState("");
  const [userRole, setUserRole] = useState(null);

  const roleOptions = [
    { value: "0", label: "Teacher" },
    { value: "1", label: "Admin" },
  ];

  React.useEffect(() => {
    AvoAxios.post("users/info", { user: user }).then((res) => {
      setUserName(res.data.username);

      var admin =
        res.data.roles && res.data.roles.indexOf("ROLE_ADMIN") !== -1 ? 1 : 0;
      setUserRole({
        label: roleOptions[admin].label,
        value: res.data.roles && res.data.roles.indexOf("ROLE_ADMIN") !== -1,
      });

      setLoading(false);
    });

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

    var admin = userRole.value === true || userRole.value === 1 ? 1 : 0; // Ensures we return an integer

    AvoAxios.post("users/update", {
      user: user,
      name: userName,
      pass: userPass,
      admin: admin,
    }).then((res) => {
      sessionStorage.setItem("toastMessage", "User updated!");
      navigate("/dashboard");
    });
  };

  return (
    <>
      <Header />
      <div className="avo-content form-page">
        {loading ? (
          <Loader />
        ) : (
          <div>
            <form className={"avo-form"} id="avo-user-update">
              <div className="avo-page-title">Update {userName}</div>
              <div className="avo-field field-input">
                <label>Name: </label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  required
                  minLength="3"
                  maxLength="32"
                  value={userName}
                  onChange={handleName}
                />
              </div>
              <div className="avo-field field-input field-password">
                <label>Password (Leave blank if unchanged): </label>
                <input type="password" id="password" onChange={handlePass} />
              </div>
              <div className="avo-field field-select">
                <label>Role: </label>
                <Select
                  options={roleOptions}
                  id="role"
                  name="role"
                  required
                  value={userRole}
                  onChange={(newValue) => setUserRole(newValue)}
                />
              </div>
              <div className="avo-form-actions">
                <input
                  type="button"
                  onClick={handleSubmit}
                  value={"Save Changes"}
                  id="form-submit"
                />
              </div>
            </form>
          </div>
        )}
        <Footer />
      </div>
    </>
  );
}
