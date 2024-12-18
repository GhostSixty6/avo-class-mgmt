import React, { Component } from "react";
import { Route, Switch, Redirect, Link, withRouter } from "react-router-dom";
import AvoAxios from "../components/AvoAxios";

import Header from "../components/Header";
import Footer from "../components/Footer";
import Loader from "../components/Loader";
import { Icon } from "@iconify/react";

class Admin extends Component {
  constructor() {
    super();

    this.state = { admins: [], teachers: [], loading: true };
  }

  componentDidMount() {
    this.getData();
  }

  getData() {
    AvoAxios.post("users/list").then((res) => {
      this.setState({
        admins: res.data.admins,
        teachers: res.data.teachers,
        loading: false,
      });
    });
  }

  render() {
    const loading = this.state.loading;

    const admins = this.state.admins;
    const adminsCount = Object.keys(admins).length;
    const adminsTitle = adminsCount == 1 ? "Admin" : "Admins";

    const teachers = this.state.teachers;
    const teacherCount = Object.keys(teachers).length;
    const teacherTitle = teacherCount == 1 ? "Teacher" : "Teachers";

    return (
      <>
        <Header />
        <div className="avo-content">
          <div className="avo-page-title">
            Manage Users
            <Link to="/user/new" className="title-update">
              <Icon icon="mdi:add-bold" />
              New User
            </Link>
          </div>
          {loading ? (
            <Loader />
          ) : (
            <div>
              <div className="avo-list-wrapper list-users">
                <div className="avo-list-title">
                  {adminsCount} {adminsTitle}
                </div>
                <div className="avo-list">
                  {Object.keys(admins).map((key, i) => (
                    <div className="list-item" key={admins[key].value}>
                      <Link
                        to={`/user/${admins[key].value}/update`}
                        className="avo-update"
                      >
                        <Icon icon="mdi:cog" />
                      </Link>
                      <Icon
                        icon="solar:user-id-bold"
                        className="avo-profile"
                        width="72px"
                        height="72px"
                      />
                      <span className="list-item-large">
                        {admins[key].label}
                      </span>
                    </div>
                  ))}
                </div>
              </div>

              <div className="avo-list-wrapper list-users">
                <div className="avo-list-title">
                  {teacherCount} {teacherTitle}
                </div>
                <div className="avo-list">
                  {Object.keys(teachers).map((key, i) => (
                    <div className="list-item" key={teachers[key].value}>
                      <Link
                        to={`/user/${teachers[key].value}/update`}
                        className="avo-update"
                      >
                        <Icon icon="mdi:cog" />
                      </Link>
                      <Icon
                        icon="solar:square-academic-cap-2-bold"
                        className="avo-profile"
                        width="72px"
                        height="72px"
                      />
                      <span className="list-item-large">
                        {teachers[key].label}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}
          <Footer />
        </div>
      </>
    );
  }
}

export default Admin;
