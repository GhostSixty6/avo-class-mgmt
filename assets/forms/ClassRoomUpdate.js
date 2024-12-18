import React, { Component, useState, useEffect } from "react";
import {
  Route,
  Switch,
  Redirect,
  Link,
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
import CreatableSelect from "react-select/creatable";

export default function ClassRoomUpdate() {
  const navigate = useNavigate();
  const { classRoom } = useParams();
  const [loading, setLoading] = useState(true);

  const [className, setClassName] = useState("");

  const [statusOptions, setStatusOptions] = useState([
    { value: "0", label: "Archived" },
    { value: "1", label: "Active" },
  ]);
  const [classStatus, setClassStatus] = useState(1);

  const [studentOptions, setStudentOptions] = useState(null);
  const [studentData, setStudentData] = useState(null);
  const [newStudentCount, setNewStudentCount] = useState(0);

  const [teacherOptions, setTeacherOptions] = useState(null);
  const [teacherData, setTeacherData] = useState(null);

  React.useEffect(() => {
    AvoAxios.post("classrooms/info", {
      classRoom: classRoom,
      allStudents: 1,
      allTeachers: 1,
    }).then((res) => {
      setClassName(res.data.className);
      setClassStatus({
        label: statusOptions[res.data.status].label,
        value: res.data.status,
      });

      setTeacherData(Object.values(res.data.teachers));
      setStudentData(Object.values(res.data.students));

      setStudentOptions(Object.values(res.data.allStudents));
      setTeacherOptions(Object.values(res.data.allTeachers));

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
    setClassName((prevClassName) => value);
  };

  const createOption = (label, value) => ({
    value: value,
    label: label,
  });

  const handleStudent = (inputValue) => {
    const newOption = createOption(
      inputValue,
      "new_student_" + newStudentCount
    );
    setStudentData((prev) => [...prev, newOption]);
    setNewStudentCount(newStudentCount + 1);
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (className === "" || className.length < 3) {
      toast.error("The name must be 4 characters or longer");
      return;
    }

    var status = classStatus.value ? 1 : 0; // Ensures we return an integer
    var teachers = [];
    var students = [];

    if (teacherData) {
      teacherData.forEach(function (item) {
        teachers[item.value] = item.label;
      });
    }

    if (studentData) {
      studentData.forEach(function (item) {
        students[item.value] = item.label;
      });
    }

    AvoAxios.post("classrooms/update", {
      classRoom: classRoom,
      name: className,
      status: status,
      teachers: teachers,
      students: students,
    }).then((res) => {
      sessionStorage.setItem("toastMessage", "Classroom updated!");
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
            <form className={"avo-form"} id="avo-class-update">
              <div className="avo-page-title">Update {className}</div>
              <div className="avo-field field-input">
                <label>Name: </label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  required
                  minLength="3"
                  maxLength="32"
                  value={className}
                  onChange={handleName}
                />
              </div>
              <div className="avo-field field-select">
                <label>Status: </label>
                <Select
                  id="status"
                  name="status"
                  required
                  options={statusOptions}
                  value={classStatus}
                  onChange={(newValue) => setClassStatus(newValue)}
                />
              </div>
              <div className="avo-field field-select">
                <label>Teachers: </label>
                <Select
                  isClearable
                  isMulti
                  id="teachers"
                  name="teachers"
                  options={teacherOptions}
                  value={teacherData}
                  onChange={(newValue) => setTeacherData(newValue)}
                />
              </div>
              <div className="avo-field field-select">
                <label>Students: </label>
                <CreatableSelect
                  isClearable
                  isMulti
                  id="students"
                  name="students"
                  onChange={(newValue) => setStudentData(newValue)}
                  onCreateOption={handleStudent}
                  options={studentOptions}
                  value={studentData}
                />
              </div>
              <div className="avo-form-actions">
                <input
                  type="button"
                  onClick={handleSubmit}
                  value={"Create Class"}
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
