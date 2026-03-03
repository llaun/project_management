import { BrowserRouter as Router, Routes, Route, Link } from "react-router-dom";

function Home() {
  return (
    <div>
      <h1>Home Page</h1>
      <p>Tavtai moril 🚀</p>
    </div>
  );
}

function About() {
  return (
    <div>
      <h1>About Page</h1>
      <p>Bidnii tuhai medeelliin huudas</p>
    </div>
  );
}

function Contact() {
  return (
    <div>
      <h1>Contact Page</h1>
      <p>Holboo barih huudas</p>
    </div>
  );
}

export default function App() {
  return (
    <Router>
      <nav style={{ display: "flex", gap: "20px", marginBottom: "20px" }}>
        <Link to="/">Home</Link>
        <Link to="/about">About</Link>
        <Link to="/contact">Contact</Link>
      </nav>

      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/about" element={<About />} />
        <Route path="/contact" element={<Contact />} />
      </Routes>
    </Router>
  );
  
}
