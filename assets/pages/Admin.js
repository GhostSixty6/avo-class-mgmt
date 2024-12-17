import React, {Component} from 'react';
import {Route, Switch,Redirect, Link, withRouter} from 'react-router-dom';

import Header from '../components/Header';
import Footer from '../components/Footer';
import Loader from '../components/Loader';
import { Icon } from "@iconify/react";
    
class Admin extends Component {
    
    render() {
        return (
            <>
            <Header />
            <div className="avo-content">
                <div className="avo-page-title">
                    Manage Users
                    <Link to="/user/new" className="title-update"><Icon icon="mdi:add-bold" />New User</Link>
                </div>
                <div className="avo-list-wrapper list-users">
    <div className="avo-list-title">2 Admins</div>
<div className="avo-list">
        <div className="list-item">
            <Icon icon="mdi:cog" className="modal-open profile-edit" />
            <Icon icon="solar:user-id-bold" className="avo-profile" width="72px" height="72px" />
            <span className="list-item-large">Mr James</span>
        </div>
        <div className="list-item">
        <Icon icon="mdi:cog" className="modal-open profile-edit" />
        <Icon icon="solar:user-id-bold" className="avo-profile" width="72px" height="72px" />
            <span className="list-item-large">Ms Smith</span>
        </div>
    </div>
</div>

<div className="avo-list-wrapper list-users">
    <div className="avo-list-title">6 Teachers</div>
<div className="avo-list">
        <div className="list-item">
        <Icon icon="mdi:cog" className="modal-open profile-edit" />
            <Icon icon="solar:square-academic-cap-2-bold" className="avo-profile" width="72px" height="72px" />
            <span className="list-item-large">Mr James</span>
        </div>
        <div className="list-item">
        <Icon icon="mdi:cog" className="modal-open profile-edit" />
        <Icon icon="solar:square-academic-cap-2-bold" className="avo-profile" />
            <span className="list-item-large">Mr Jeremy</span>
        </div>
        <div className="list-item">
        <Icon icon="mdi:cog" className="modal-open profile-edit" />
        <Icon icon="solar:square-academic-cap-2-bold" className="avo-profile" />
            <span className="list-item-large">Mr Arnold</span>
        </div>
        <div className="list-item">
        <Icon icon="mdi:cog" className="modal-open profile-edit" />
        <Icon icon="solar:square-academic-cap-2-bold" className="avo-profile" />
            <span className="list-item-large">Ms Samantha</span>
        </div>
        <div className="list-item">
        <Icon icon="mdi:cog" className="modal-open profile-edit" />
        <Icon icon="solar:square-academic-cap-2-bold" className="avo-profile" />
            <span className="list-item-large">Ms Alicia</span>
        </div>
        <div className="list-item">
        <Icon icon="mdi:cog" className="modal-open profile-edit" />
        <Icon icon="solar:square-academic-cap-2-bold" className="avo-profile" />
            <span className="list-item-large">Ms Taylor</span>
        </div>
    </div>
</div>
<Footer />
</div>
        
            
            </>
        )
    }
}
    
export default Admin;