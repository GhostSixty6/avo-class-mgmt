import React, {Component} from 'react';
    
class Home extends Component {

    // TODO check if authenticated or not, then show either page
    avoRedirect() {        
        if(sessionStorage.getItem('userToken') !== null) {
            window.location.href = '/dashboard';
            
        } else {
            window.location.href = '/login';
        }
    }
    
    render() {

        return (
            <>
            {this.avoRedirect()}
            </>
        )
    }
}
    
export default Home;