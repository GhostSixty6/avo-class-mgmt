import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import axios from 'axios';

import Footer from '../components/Footer';

const Login = (props) => {
  const [name, setName] = useState('')
  const [password, setPassword] = useState('')

  const navigate = useNavigate()

  if(sessionStorage.getItem('userToken')) {
    navigate('/dashboard')
  }

  const onButtonClick = () => {
  
    // Check if the user has entered both fields correctly
    if ('' === name) {
      alert('Please enter your username')
      return
    }
  
    if ('' === password) {
      alert('Please enter a password')
      return
    }
  
    if (password.length < 4) {
      alert('The password must be 4 characters or longer')
      return
    }

    axios.post('/api/login_check', {'username': name, 'password': password}).then(res => {
      sessionStorage.setItem('userName', name )
      sessionStorage.setItem('userToken', res.data.token )

      axios.post('/api/users', {'action': 'info'}, {headers: { Authorization: `Bearer ${res.data.token}` }
      }).then(res => {
        sessionStorage.setItem('userRoles', res.data.roles )
        sessionStorage.setItem('userId', res.data.roles )

      })
      .catch(function (error) {
          alert('Failed to login')
          sessionStorage.clear();
          navigate('/')
      });

      navigate('/dashboard')
    })
    .catch(function (error) {
      alert('Incorrect Username or Password')
      return
    });

  }

  return (
    <>
    <div className="avo-content form-page">
      <form className={'avo-form'} id="avo-login">
            <h1>Login</h1>
        <div className="avo-field field-input">
          <input type="text" value={name} placeholder="Enter your Username" onChange={(ev) => setName(ev.target.value)} />
        </div>
        <div className="avo-field field-input field-password">
          <input type="password" value={password} placeholder="Enter your Password" onChange={(ev) => setPassword(ev.target.value)} />
        </div>
        <div className="avo-form-actions">
          <input type="button" onClick={onButtonClick} value={'Log in'} />
        </div>
      </form>
      <Footer />
    </div>
 
    </>
)
}

export default Login