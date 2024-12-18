import React, { Component } from "react";
import { Link } from "react-router";

class Footer extends Component {
  getYear() {
    return new Date().getFullYear();
  }

  render() {
    return (
      <div className="avo-footer">
        <svg
          id="avo-bg"
          width="100%"
          height="100%"
          xmlns="http://www.w3.org/2000/svg"
        >
          <defs>
            <pattern
              id="a"
              patternUnits="userSpaceOnUse"
              width="20"
              height="20"
              patternTransform="scale(2) rotate(0)"
            >
              <rect
                x="0"
                y="0"
                width="100%"
                height="100%"
                fill="#f0fff3ff"
              ></rect>
              <path
                d="M 10,-2.55e-7 V 20 Z M -1.1677362e-8,10 H 20 Z"
                strokeWidth="1"
                stroke="#a9dcb5ff"
                fill="none"
              ></path>
            </pattern>
          </defs>
          <rect
            width="800%"
            height="800%"
            transform="translate(0,0)"
            fill="url(#a)"
          ></rect>
        </svg>
        <div className="footer-copyright">Copyright © {this.getYear()}</div>
        <div className="footer-author">
          Created by{" "}
          <Link to="https://github.com/GhostSixty6">Mitch Stroebel</Link>
        </div>
      </div>
    );
  }
}

export default Footer;
