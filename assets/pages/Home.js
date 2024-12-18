import React, { Component } from "react";

class Home extends Component {
  avoRedirect() {
    if (sessionStorage.getItem("userToken") !== null) {
      // Go to dashboard if logged in
      window.location.href = "/dashboard";
    } else {
      // Otherwise go to login form
      window.location.href = "/login";
    }
  }

  render() {
    return <>{this.avoRedirect()}</>;
  }
}

export default Home;
