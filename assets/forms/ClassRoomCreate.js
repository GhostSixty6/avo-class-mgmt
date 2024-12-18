import React, { Component, useState, useEffect } from "react";
import {
  Route,
  Switch,
  Redirect,
  withRouter,
  useNavigate,
} from "react-router-dom";
import toast, { Toaster } from "react-hot-toast";

import AvoAxios from "../components/AvoAxios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import Loader from "../components/Loader";

import Select from "react-select";
import CreatableSelect from "react-select/creatable";

export default function ClassRoomCreate() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [className, setClassName] = useState("");

  const [studentOptions, setStudentOptions] = useState(null);
  const [studentData, setStudentData] = useState(null);
  const [newStudentCount, setNewStudentCount] = useState(0);

  const [teacherOptions, setTeacherOptions] = useState(null);
  const [teacherData, setTeacherData] = useState(null);

  React.useEffect(() => {
    AvoAxios.post("students/list", { allTeachers: 1 }).then((res) => {
      setLoading(false);
      setStudentOptions(Object.values(res.data.students));
      setTeacherOptions(Object.values(res.data.allTeachers));
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
      name: className,
      teachers: teachers,
      students: students,
    }).then((res) => {
      sessionStorage.setItem("toastMessage", "New classroom created!");
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
            <form className={"avo-form"} id="avo-class-create">
              <div className="avo-page-title">Create new class</div>
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
                <label>Teachers: </label>
                <Select
                  isClearable
                  isMulti
                  options={teacherOptions}
                  id="teachers"
                  name="teachers"
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
