import React, { Component } from "react";
import {
  Route,
  Switch,
  Redirect,
  Link,
  withRouter,
  useParams,
} from "react-router-dom";
import AvoAxios from "../components/AvoAxios";

import Header from "../components/Header";
import Footer from "../components/Footer";
import Loader from "../components/Loader";
import { Icon } from "@iconify/react";

function withParams(Component) {
  return (props) => <Component {...props} params={useParams()} />;
}

class ClassRoom extends Component {
  constructor() {
    super();
    this.state = {
      classRoom: 0,
      students: [],
      teachers: [],
      currentClassName: "Class",
      loading: true,
    };
  }

  componentDidMount() {
    let { classRoom } = this.props.params;
    this.getClassRoom(classRoom);
  }

  getClassRoom(classRoom) {
    AvoAxios.post("classrooms/info", { classRoom: classRoom }).then((res) => {
      this.setState({
        classRoom: classRoom,
        students: res.data.students,
        teachers: res.data.teachers,
        currentClassName: res.data.className,
        loading: false,
      });
    });
  }

  render() {
    const loading = this.state.loading;
    const classRoom = this.state.classRoom;
    const currentClassName = this.state.currentClassName;

    const teachers = this.state.teachers;
    const teacherCount = Object.keys(teachers).length;
    const teacherTitle = teacherCount == 1 ? "Teacher" : "Teachers";

    const students = this.state.students;
    const studentCount = Object.keys(students).length;
    const studentTitle = studentCount == 1 ? "Student" : "Students";

    return (
      <>
        <Header />
        <div className="avo-content">
          {loading ? (
            <Loader />
          ) : (
            <div>
              <div className="avo-page-title">
                {currentClassName}
                <Link
                  to={`/classroom/${classRoom}/update`}
                  className="title-update"
                >
                  <Icon icon="solar:settings-bold" /> Update Class
                </Link>
              </div>

              <div className="avo-list-wrapper list-users">
                <div className="avo-list-title">
                  {teacherCount} {teacherTitle}
                </div>
                <div className="avo-list">
                  {Object.keys(teachers).map((key, i) => (
                    <div className="list-item" key={key}>
                      <Icon
                        icon="solar:user-id-bold"
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

              <div className="avo-list-wrapper list-users">
                <div className="avo-list-title">
                  {studentCount} {studentTitle}
                </div>
                <div className="avo-list">
                  {Object.keys(students).map((key, i) => (
                    <div className="list-item" key={key}>
                      <Icon
                        icon="solar:square-academic-cap-2-bold"
                        className="avo-profile"
                        width="72px"
                        height="72px"
                      />
                      <span className="list-item-large">
                        {students[key].label}
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

export default withParams(ClassRoom);
