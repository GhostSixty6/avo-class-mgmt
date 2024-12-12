import React, {Component} from 'react';
import axios from 'axios';
    
    
class Classes extends Component {
    constructor() {
        super();
        
        this.state = { classes: [], loading: true}
    }
    
    componentDidMount() {
        this.getClasses();
    }
    
    getClasses() {
        axios.get('/api/classes/').then(res => {
            const classes = res.data.slice(0,15);
            this.setState({ classes, loading: false })
        })
    }
    
    render() {
        const loading = this.state.loading;
        return (
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row">
                            <h2 className="text-center"><span>List of classes</span></h2>
                        </div>
    
                        {loading ? (
                            <div className={'row text-center'}>
                                <span className="fa fa-spin fa-spinner fa-4x"></span>
                            </div>
    
                        ) : (
                            <div className={'row'}>
                                { this.state.classes.map(classObj =>
                                    <div className="col-md-10 offset-md-1 row-block" key={classObj.id}>
                                        <ul id="sortable">
                                            <li>
                                                <div className="media">
                                                    <div className="media-body">
                                                        <h4>{classObj.name}</h4>
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
    
export default Classes;