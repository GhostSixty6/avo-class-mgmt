import React, { Component } from "react";

import { Link } from "react-router";
import { Icon } from "@iconify/react";

class Header extends Component {
  avoLogout() {
    sessionStorage.clear();
    window.location.href = "/";
  }

  render() {
    return (
      <div className="avo_top_menu">
        <div className="avo_top_menu_left">
          <Link to="/dashboard">
            <Icon
              icon="hugeicons:avocado"
              className="avo-logo"
              width="64px"
              height="64px"
            />
          </Link>
        </div>

        <nav className="avo_top_menu_nav">
          <ul>
            <li>
              <Link to="/dashboard">
                <Icon
                  icon="solar:presentation-graph-bold"
                  width="36px"
                  height="36px"
                />
                Classes
              </Link>
            </li>
            {sessionStorage.getItem("userRoles") &&
            sessionStorage.getItem("userRoles").indexOf("ROLE_ADMIN") !== -1 ? (
              <li>
                <Link to="/admin">
                  <Icon icon="solar:settings-bold" width="36px" height="36px" />
                  Admin
                </Link>
              </li>
            ) : null}
          </ul>
        </nav>

        <div className="avo_top_menu_right">
          <button onClick={this.avoLogout}>
            <Icon icon="solar:logout-3-bold" width="36px" height="36px" />
            Logout
          </button>
        </div>
      </div>
    );
  }
}

export default Header;
