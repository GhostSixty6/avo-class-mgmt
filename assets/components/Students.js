import React, {Component} from 'react';
import axios from 'axios';
    
class Students extends Component {
    constructor() {
        super();
        this.state = { students: [], loading: true};
    }
    
    componentDidMount() {
        this.getStudents();
    }
    
    getStudents() {
       axios.get(`http://localhost:8000/api/students`).then(students => {
           this.setState({ students: students.data, loading: false})
       })
    }
    
    render() {
        const loading = this.state.loading;
        return(
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row">
                            <h2 className="text-center"><span>List of students</span></h2>
                        </div>
                        {loading ? (
                            <div className={'row text-center'}>
                                <span className="fa fa-spin fa-spinner fa-4x"></span>
                            </div>
                        ) : (
                            <div className={'row'}>
                                { this.state.students.map(student =>
                                    <div className="col-md-10 offset-md-1 row-block" key={student.id}>
                                        <ul id="sortable">
                                            <li>
                                                <div className="media">
                                                    <div className="media-body">
                                                        <h4>{student.name}</h4>
                                                    </div>
                                                    <div className="media-right align-self-center">
                                                        <a href="#" className="btn btn-default">Contact Now</a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </section>
            </div>
        )
    }
}
export default Students;