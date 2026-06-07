import gsap from "gsap";

const entrances = function () {
  gsap.from(".fade-in", { opacity: 0 })
  gsap.from(".scale-in", { scale: 0 })
  gsap.from(".slide-in-top", { y: -100 })
  gsap.from(".slide-in-bottom", { y: 100 })
  gsap.from(".slide-in-left", { x: -100 })
  gsap.from(".slide-in-right", { x: 100 })
  gsap.from(".slide-in-tl", { x: -100, y: -100 })
  gsap.from(".slide-in-tr", { x: 100, y: -100 })
  gsap.from(".slide-in-br", { x: 100, y: 100 })
  gsap.from(".slide-in-bl", { x: -100, y: 100 });
};

export default entrances;
