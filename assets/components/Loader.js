import React, {Component} from 'react';
    
class Loader extends Component {

    render() {
        return (
      <div className="avo-skel">
        <div className="avo-skel-title"></div>
        <div className="avo-skel-list small">
            <div className="avo-skel-list-item"></div><div className="avo-skel-list-item"></div><div className="avo-skel-list-item"></div>
        </div>
        <div className="avo-skel-title small"></div>
        <div className="avo-skel-list">
            <div className="avo-skel-list-item"></div><div className="avo-skel-list-item"></div><div className="avo-skel-list-item"></div>
        </div>
      </div>
        )
    }
}
    
export default Loader;