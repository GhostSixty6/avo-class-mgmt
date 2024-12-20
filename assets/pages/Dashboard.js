import React, { Component } from "react";
import { Route, Switch, Redirect, Link, withRouter } from "react-router-dom";
import toast, { Toaster } from "react-hot-toast";
import { Icon } from "@iconify/react";

import AvoAxios from "../components/AvoAxios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import Loader from "../components/Loader";

class Dashboard extends Component {
  constructor() {
    super();

    this.state = {
      classrooms: [],
      userName: sessionStorage.getItem("userName"),
      loading: true,
    };
  }

  componentDidMount() {
    this.getClassRooms();

    const toastMessage = sessionStorage.getItem("toastMessage");

    if (toastMessage) {
      toast.success(toastMessage);
      sessionStorage.removeItem("toastMessage");
    }
  }

  getClassRooms() {
    AvoAxios.post("classrooms/list").then((res) => {
      this.setState({
        classrooms: res.data,
        userName: this.state.userName,
        loading: false,
        toastShown: false,
      });
    });
  }

  render() {
    const loading = this.state.loading;
    const stats = this.state.classrooms.stats;
    const currentClasses = this.state.classrooms.current;
    const allClasses = this.state.classrooms.all;
    const userName = this.state.userName;

    return (
      <>
        <Header />

        <div className="avo-content">
          <div className="avo-page-title">Welcome Back, {userName}!</div>

          {loading ? (
            <Loader />
          ) : (
            <div>
              <div className="avo-list-wrapper list-stats">
                <div className="avo-list">
                  <div className="list-item">
                    <Icon
                      icon="solar:presentation-graph-bold"
                      width="56px"
                      height="56px"
                    />
                    <span className="list-item-large">{stats.classrooms}</span>
                    <span className="list-item-small">Classes</span>
                  </div>
                  <div className="list-item">
                    <Icon
                      icon="solar:square-academic-cap-2-bold"
                      width="56px"
                      height="56px"
                    />
                    <span className="list-item-large">{stats.students}</span>
                    <span className="list-item-small">Students</span>
                  </div>
                  {Object.hasOwn(stats, "teachers") ? (
                    <div className="list-item">
                      <Icon
                        icon="solar:user-id-bold"
                        width="56px"
                        height="56px"
                      />
                      <span className="list-item-large">{stats.teachers}</span>
                      <span className="list-item-small">Teachers</span>
                    </div>
                  ) : null}
                  {Object.hasOwn(stats, "admins") ? (
                    <div className="list-item">
                      <Icon
                        icon="solar:shield-user-bold"
                        width="56px"
                        height="56px"
                      />
                      <span className="list-item-large">{stats.admins}</span>
                      <span className="list-item-small">Admins</span>
                    </div>
                  ) : null}
                </div>
              </div>
              <div className="avo-list-wrapper list-classes">
                <div className="avo-list-title">
                  Your Classes
                  <Link to="/classroom/new" className="title-update">
                    <Icon icon="mdi:add-bold" /> New Class
                  </Link>
                </div>
                <div className="avo-list">
                  {Object.keys(currentClasses).map((key, i) => (
                    <Link
                      to={`/classroom/${currentClasses[key].id}`}
                      className="list-item"
                      key={currentClasses[key].id}
                    >
                      <Icon
                        icon="solar:presentation-graph-bold"
                        width="64px"
                        height="64px"
                      />
                      <span className="list-item-large">
                        {currentClasses[key].name}
                      </span>
                      <span className="list-item-small">
                        {currentClasses[key].students} Student(s) |{" "}
                        {currentClasses[key].teachers} Teacher(s)
                      </span>
                    </Link>
                  ))}
                </div>
              </div>
              {Object.keys(allClasses).length > 0 ? (
                <div className="avo-list-wrapper list-classes">
                  <div className="avo-list-title">All Classes</div>
                  <div className="avo-list">
                    {Object.keys(allClasses).map((key, i) => (
                      <Link
                        to={`/classroom/${allClasses[key].id}`}
                        className="list-item"
                        key={allClasses[key].id}
                      >
                        <Icon
                          icon="solar:presentation-graph-bold"
                          width="64px"
                          height="64px"
                        />
                        <span className="list-item-large">
                          {allClasses[key].name}
                        </span>
                        <span className="list-item-small">
                          {allClasses[key].students} Student(s) |{" "}
                          {allClasses[key].teachers} Teacher(s)
                        </span>
                      </Link>
                    ))}
                  </div>
                </div>
              ) : null}
            </div>
          )}
          <Footer />
        </div>
      </>
    );
  }
}

export default Dashboard;
