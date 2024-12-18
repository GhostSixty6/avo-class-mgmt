import React, { Component } from "react";
import { Route, Switch, Redirect, Link, withRouter } from "react-router-dom";

class Page404 extends Component {
  render() {
    return (
      <>
        <div className="avo-content page-error">
          <div className="avo-error">
            <div className="avo-page-title">Page Not Found</div>
            <Link to="/" className="button">
              Go Home
            </Link>
          </div>
        </div>
      </>
    );
  }
}

export default Page404;
