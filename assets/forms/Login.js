import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import toast, { Toaster } from "react-hot-toast";

import Footer from "../components/Footer";

const Login = (props) => {
  const [name, setName] = useState("");
  const [password, setPassword] = useState("");

  const navigate = useNavigate();

  if (sessionStorage.getItem("userToken")) {
    navigate("/dashboard");
  }

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

  const onButtonClick = () => {
    // Check if the user has entered both fields correctly
    if (name === "" || name.length < 3) {
      toast.error("The username must be 4 characters or longer");
      return;
    }

    if (password === "" || password.length < 4) {
      toast.error("The password must be 4 characters or longer");
      return;
    }

    axios
      .post("/api/login_check", { username: name, password: password })
      .then((res) => {
        sessionStorage.setItem("userToken", res.data.token);
        sessionStorage.setItem("userName", res.data.userName);
        sessionStorage.setItem("userRoles", res.data.userRoles);

        sessionStorage.setItem("toastMessage", "You are now logged in!");
        navigate("/dashboard");
      })
      .catch(function (error) {
        toast.error("Incorrect Username or Password");
        return;
      });
  };

  return (
    <>
      <div className="avo-content form-page">
        <form className={"avo-form"} id="avo-login">
          <h1>Login</h1>
          <div className="avo-field field-input">
            <input
              type="text"
              value={name}
              required
              minLength="3"
              maxLength="32"
              placeholder="Enter your Username"
              autoComplete="username"
              onChange={(ev) => setName(ev.target.value)}
            />
          </div>
          <div className="avo-field field-input field-password">
            <input
              type="password"
              value={password}
              required
              minLength="4"
              maxLength="32"
              placeholder="Enter your Password"
              autoComplete="current-password"
              onChange={(ev) => setPassword(ev.target.value)}
            />
          </div>
          <div className="avo-form-actions">
            <input
              type="button"
              onClick={onButtonClick}
              value={"Log in"}
              id="form-submit"
            />
          </div>
        </form>
        <Footer />
      </div>
    </>
  );
};

export default Login;
