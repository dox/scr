var app = function() {
	"use strict";
	function e() {}
	const s = e => e;
	function a(e) {
		return e()
	}
	function t() {
		return Object.create(null)
	}
	function o(e) {
		e.forEach(a)
	}
	function r(e) {
		return "function" == typeof e
	}
	function n(e, s) {
		return e != e ? s == s : e !== s || e && "object" == typeof e || "function" == typeof e
	}
	function i(s, a, t) {
		s.$$.on_destroy.push(function(s, ...a) {
			if (null == s)
				return e;
			const t = s.subscribe(...a);
			return t.unsubscribe ? () => t.unsubscribe() : t
		}(a, t))
	}
	function l(e, s, a, t) {
		if (e) {
			const o = u(e, s, a, t);
			return e[0](o)
		}
	}
	function u(e, s, a, t) {
		return e[1] && t ? function(e, s) {
			for (const a in s)
				e[a] = s[a];
			return e
		}(a.ctx.slice(), e[1](t(s))) : a.ctx
	}
	function c(e, s, a, t) {
		if (e[2] && t) {
			const o = e[2](t(a));
			if (void 0 === s.dirty)
				return o;
			if ("object" == typeof o) {
				const e = [],
					a = Math.max(s.dirty.length, o.length);
				for (let t = 0; t < a; t += 1)
					e[t] = s.dirty[t] | o[t];
				return e
			}
			return s.dirty | o
		}
		return s.dirty
	}
	function d(e, s, a, t, o, r) {
		if (o) {
			const n = u(s, a, t, r);
			e.p(n, o)
		}
	}
	function p(e) {
		if (e.ctx.length > 32) {
			const s = [],
				a = e.ctx.length / 32;
			for (let e = 0; e < a; e++)
				s[e] = -1;
			return s
		}
		return -1
	}
	function m(e) {
		let s = !1;
		return function(...a) {
			s || (s = !0, e.call(this, ...a))
		}
	}
	function h(e) {
		return null == e ? "" : e
	}
	function y(e, s, a) {
		return e.set(a), s
	}
	const g = "undefined" != typeof window;
	let f = g ? () => window.performance.now() : () => Date.now(),
		b = g ? e => requestAnimationFrame(e) : e;
	const k = new Set;
	function w(e) {
		k.forEach((s => {
			s.c(e) || (k.delete(s), s.f())
		})),
		0 !== k.size && b(w)
	}
	function v(e) {
		let s;
		return 0 === k.size && b(w), {
			promise: new Promise((a => {
				k.add(s = {
					c: e,
					f: a
				})
			})),
			abort() {
				k.delete(s)
			}
		}
	}
	function $(e, s) {
		e.appendChild(s)
	}
	function x(e) {
		if (!e)
			return document;
		const s = e.getRootNode ? e.getRootNode() : e.ownerDocument;
		return s && s.host ? s : e.ownerDocument
	}
	function z(e) {
		const s = S("style");
		return function(e, s) {
			$(e.head || e, s)
		}(x(e), s), s.sheet
	}
	function j(e, s, a) {
		e.insertBefore(s, a || null)
	}
	function q(e) {
		e.parentNode.removeChild(e)
	}
	function C(e, s) {
		for (let a = 0; a < e.length; a += 1)
			e[a] && e[a].d(s)
	}
	function S(e) {
		return document.createElement(e)
	}
	function N(e) {
		return document.createElementNS("http://www.w3.org/2000/svg", e)
	}
	function M(e) {
		return document.createTextNode(e)
	}
	function T() {
		return M(" ")
	}
	function O() {
		return M("")
	}
	function E(e, s, a, t) {
		return e.addEventListener(s, a, t), () => e.removeEventListener(s, a, t)
	}
	function _(e) {
		return function(s) {
			return s.preventDefault(), e.call(this, s)
		}
	}
	function L(e) {
		return function(s) {
			s.target === this && e.call(this, s)
		}
	}
	function A(e, s, a) {
		null == a ? e.removeAttribute(s) : e.getAttribute(s) !== a && e.setAttribute(s, a)
	}
	function H(e, s) {
		s = "" + s,
		e.wholeText !== s && (e.data = s)
	}
	function I(e, s, a, t) {
		null === a ? e.style.removeProperty(s) : e.style.setProperty(s, a, t ? "important" : "")
	}
	function D(e, s) {
		for (let a = 0; a < e.options.length; a += 1) {
			const t = e.options[a];
			if (t.__value === s)
				return void (t.selected = !0)
		}
		e.selectedIndex = -1
	}
	function R(e, s, a) {
		e.classList[a ? "add" : "remove"](s)
	}
	function U(e, s, a=!1) {
		const t = document.createEvent("CustomEvent");
		return t.initCustomEvent(e, a, !1, s), t
	}
	const B = new Map;
	let W,
		G = 0;
	function P(e, s, a, t, o, r, n, i=0) {
		const l = 16.666 / t;
		let u = "{\n";
		for (let e = 0; e <= 1; e += l) {
			const t = s + (a - s) * r(e);
			u += 100 * e + `%{${n(t, 1 - t)}}\n`
		}
		const c = u + `100% {${n(a, 1 - a)}}\n}`,
			d = `__svelte_${function(e) {let s = 5381,a = e.length;for (; a--;)s = (s << 5) - s ^ e.charCodeAt(a);return s >>> 0}(c)}_${i}`,
			p = x(e),
			{stylesheet: m, rules: h} = B.get(p) || function(e, s) {
				const a = {
					stylesheet: z(s),
					rules: {}
				};
				return B.set(e, a), a
			}(p, e);
		h[d] || (h[d] = !0, m.insertRule(`@keyframes ${d} ${c}`, m.cssRules.length));
		const y = e.style.animation || "";
		return e.style.animation = `${y ? `${y}, ` : ""}${d} ${t}ms linear ${o}ms 1 both`, G += 1, d
	}
	function J(e, s) {
		const a = (e.style.animation || "").split(", "),
			t = a.filter(s ? e => e.indexOf(s) < 0 : e => -1 === e.indexOf("__svelte")),
			o = a.length - t.length;
		o && (e.style.animation = t.join(", "), G -= o, G || b((() => {
			G || (B.forEach((e => {
				const {stylesheet: s} = e;
				let a = s.cssRules.length;
				for (; a--;)
					s.deleteRule(a);
				e.rules = {}
			})), B.clear())
		})))
	}
	function Y(e) {
		W = e
	}
	function V() {
		if (!W)
			throw new Error("Function called outside component initialization");
		return W
	}
	function X(e) {
		V().$$.on_mount.push(e)
	}
	function F(e) {
		V().$$.on_destroy.push(e)
	}
	function K() {
		const e = V();
		return (s, a) => {
			const t = e.$$.callbacks[s];
			if (t) {
				const o = U(s, a);
				t.slice().forEach((s => {
					s.call(e, o)
				}))
			}
		}
	}
	function Z(e, s) {
		V().$$.context.set(e, s)
	}
	function Q(e) {
		return V().$$.context.get(e)
	}
	const ee = [],
		se = [],
		ae = [],
		te = [],
		oe = Promise.resolve();
	let re = !1;
	function ne(e) {
		ae.push(e)
	}
	function ie(e) {
		te.push(e)
	}
	const le = new Set;
	let ue,
		ce = 0;
	function de() {
		const e = W;
		do {
			for (; ce < ee.length;) {
				const e = ee[ce];
				ce++,
				Y(e),
				pe(e.$$)
			}
			for (Y(null), ee.length = 0, ce = 0; se.length;)
				se.pop()();
			for (let e = 0; e < ae.length; e += 1) {
				const s = ae[e];
				le.has(s) || (le.add(s), s())
			}
			ae.length = 0
		} while (ee.length);
		for (; te.length;)
			te.pop()();
		re = !1,
		le.clear(),
		Y(e)
	}
	function pe(e) {
		if (null !== e.fragment) {
			e.update(),
			o(e.before_update);
			const s = e.dirty;
			e.dirty = [-1],
			e.fragment && e.fragment.p(e.ctx, s),
			e.after_update.forEach(ne)
		}
	}
	function me() {
		return ue || (ue = Promise.resolve(), ue.then((() => {
			ue = null
		}))), ue
	}
	function he(e, s, a) {
		e.dispatchEvent(U(`${s ? "intro" : "outro"}${a}`))
	}
	const ye = new Set;
	let ge;
	function fe() {
		ge = {
			r: 0,
			c: [],
			p: ge
		}
	}
	function be() {
		ge.r || o(ge.c),
		ge = ge.p
	}
	function ke(e, s) {
		e && e.i && (ye.delete(e), e.i(s))
	}
	function we(e, s, a, t) {
		if (e && e.o) {
			if (ye.has(e))
				return;
			ye.add(e),
			ge.c.push((() => {
				ye.delete(e),
				t && (a && e.d(1), t())
			})),
			e.o(s)
		}
	}
	const ve = {
		duration: 0
	};
	function $e(a, t, n, i) {
		let l = t(a, n),
			u = i ? 0 : 1,
			c = null,
			d = null,
			p = null;
		function m() {
			p && J(a, p)
		}
		function h(e, s) {
			const a = e.b - u;
			return s *= Math.abs(a), {
				a: u,
				b: e.b,
				d: a,
				duration: s,
				start: e.start,
				end: e.start + s,
				group: e.group
			}
		}
		function y(t) {
			const {delay: r=0, duration: n=300, easing: i=s, tick: y=e, css: g} = l || ve,
				b = {
					start: f() + r,
					b: t
				};
			t || (b.group = ge, ge.r += 1),
			c || d ? d = b : (g && (m(), p = P(a, u, t, n, r, i, g)), t && y(0, 1), c = h(b, n), ne((() => he(a, t, "start"))), v((e => {
				if (d && e > d.start && (c = h(d, n), d = null, he(a, c.b, "start"), g && (m(), p = P(a, u, c.b, c.duration, 0, i, l.css))), c)
					if (e >= c.end)
						y(u = c.b, 1 - u),
						he(a, c.b, "end"),
						d || (c.b ? m() : --c.group.r || o(c.group.c)),
						c = null;
					else if (e >= c.start) {
						const s = e - c.start;
						u = c.a + c.d * i(s / c.duration),
						y(u, 1 - u)
					}
				return !(!c && !d)
			})))
		}
		return {
			run(e) {
				r(l) ? me().then((() => {
					l = l(),
					y(e)
				})) : y(e)
			},
			end() {
				m(),
				c = d = null
			}
		}
	}
	function xe(e, s) {
		const a = s.token = {};
		function t(e, t, o, r) {
			if (s.token !== a)
				return;
			s.resolved = r;
			let n = s.ctx;
			void 0 !== o && (n = n.slice(), n[o] = r);
			const i = e && (s.current = e)(n);
			let l = !1;
			s.block && (s.blocks ? s.blocks.forEach(((e, a) => {
				a !== t && e && (fe(), we(e, 1, 1, (() => {
					s.blocks[a] === e && (s.blocks[a] = null)
				})), be())
			})) : s.block.d(1), i.c(), ke(i, 1), i.m(s.mount(), s.anchor), l = !0),
			s.block = i,
			s.blocks && (s.blocks[t] = i),
			l && de()
		}
		if ((o = e) && "object" == typeof o && "function" == typeof o.then) {
			const a = V();
			if (e.then((e => {
				Y(a),
				t(s.then, 1, s.value, e),
				Y(null)
			}), (e => {
				if (Y(a), t(s.catch, 2, s.error, e), Y(null), !s.hasCatch)
					throw e
			})), s.current !== s.pending)
				return t(s.pending, 0), !0
		} else {
			if (s.current !== s.then)
				return t(s.then, 1, s.value, e), !0;
			s.resolved = e
		}
		var o
	}
	function ze(e, s) {
		e.d(1),
		s.delete(e.key)
	}
	function je(e, s, a) {
		const t = e.$$.props[s];
		void 0 !== t && (e.$$.bound[t] = a, a(e.$$.ctx[t]))
	}
	function qe(e) {
		e && e.c()
	}
	function Ce(e, s, t, n) {
		const {fragment: i, on_mount: l, on_destroy: u, after_update: c} = e.$$;
		i && i.m(s, t),
		n || ne((() => {
			const s = l.map(a).filter(r);
			u ? u.push(...s) : o(s),
			e.$$.on_mount = []
		})),
		c.forEach(ne)
	}
	function Se(e, s) {
		const a = e.$$;
		null !== a.fragment && (o(a.on_destroy), a.fragment && a.fragment.d(s), a.on_destroy = a.fragment = null, a.ctx = [])
	}
	function Ne(e, s) {
		-1 === e.$$.dirty[0] && (ee.push(e), re || (re = !0, oe.then(de)), e.$$.dirty.fill(0)),
		e.$$.dirty[s / 31 | 0] |= 1 << s % 31
	}
	function Me(s, a, r, n, i, l, u, c=[-1]) {
		const d = W;
		Y(s);
		const p = s.$$ = {
			fragment: null,
			ctx: null,
			props: l,
			update: e,
			not_equal: i,
			bound: t(),
			on_mount: [],
			on_destroy: [],
			on_disconnect: [],
			before_update: [],
			after_update: [],
			context: new Map(a.context || (d ? d.$$.context : [])),
			callbacks: t(),
			dirty: c,
			skip_bound: !1,
			root: a.target || d.$$.root
		};
		u && u(p.root);
		let m = !1;
		if (p.ctx = r ? r(s, a.props || {}, ((e, a, ...t) => {
			const o = t.length ? t[0] : a;
			return p.ctx && i(p.ctx[e], p.ctx[e] = o) && (!p.skip_bound && p.bound[e] && p.bound[e](o), m && Ne(s, e)), a
		})) : [], p.update(), m = !0, o(p.before_update), p.fragment = !!n && n(p.ctx), a.target) {
			if (a.hydrate) {
				const e = function(e) {
					return Array.from(e.childNodes)
				}(a.target);
				p.fragment && p.fragment.l(e),
				e.forEach(q)
			} else
				p.fragment && p.fragment.c();
			a.intro && ke(s.$$.fragment),
			Ce(s, a.target, a.anchor, a.customElement),
			de()
		}
		Y(d)
	}
	class Te {
		$destroy()
		{
			Se(this, 1),
			this.$destroy = e
		}
		$on(e, s)
		{
			const a = this.$$.callbacks[e] || (this.$$.callbacks[e] = []);
			return a.push(s), () => {
				const e = a.indexOf(s);
				-1 !== e && a.splice(e, 1)
			}
		}
		$set(e)
		{
			var s;
			this.$$set && (s = e, 0 !== Object.keys(s).length) && (this.$$.skip_bound = !0, this.$$set(e), this.$$.skip_bound = !1)
		}
	}
	var Oe = "undefined" != typeof globalThis ? globalThis : "undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : {},
		Ee = {
			exports: {}
		};
	(function(e, s, a) {
		function t(e) {
			var s,
				a = this,
				t = (s = 4022871197, function(e) {
					e = String(e);
					for (var a = 0; a < e.length; a++) {
						var t = .02519603282416938 * (s += e.charCodeAt(a));
						t -= s = t >>> 0,
						s = (t *= s) >>> 0,
						s += 4294967296 * (t -= s)
					}
					return 2.3283064365386963e-10 * (s >>> 0)
				});
			a.next = function() {
				var e = 2091639 * a.s0 + 2.3283064365386963e-10 * a.c;
				return a.s0 = a.s1, a.s1 = a.s2, a.s2 = e - (a.c = 0 | e)
			},
			a.c = 1,
			a.s0 = t(" "),
			a.s1 = t(" "),
			a.s2 = t(" "),
			a.s0 -= t(e),
			a.s0 < 0 && (a.s0 += 1),
			a.s1 -= t(e),
			a.s1 < 0 && (a.s1 += 1),
			a.s2 -= t(e),
			a.s2 < 0 && (a.s2 += 1),
			t = null
		}
		function o(e, s) {
			return s.c = e.c, s.s0 = e.s0, s.s1 = e.s1, s.s2 = e.s2, s
		}
		function r(e, s) {
			var a = new t(e),
				r = s && s.state,
				n = a.next;
			return n.int32 = function() {
				return 4294967296 * a.next() | 0
			}, n.double = function() {
				return n() + 11102230246251565e-32 * (2097152 * n() | 0)
			}, n.quick = n, r && ("object" == typeof r && o(r, a), n.state = function() {
				return o(a, {})
			}), n
		}
		s && s.exports ? s.exports = r : a && a.amd ? a((function() {
			return r
		})) : this.alea = r
	})(0, Ee, !1);
	var _e = {
		exports: {}
	};
	!function(e) {
		!function(e, s, a) {
			function t(e) {
				var s = this,
					a = "";
				s.x = 0,
				s.y = 0,
				s.z = 0,
				s.w = 0,
				s.next = function() {
					var e = s.x ^ s.x << 11;
					return s.x = s.y, s.y = s.z, s.z = s.w, s.w ^= s.w >>> 19 ^ e ^ e >>> 8
				},
				e === (0 | e) ? s.x = e : a += e;
				for (var t = 0; t < a.length + 64; t++)
					s.x ^= 0 | a.charCodeAt(t),
					s.next()
			}
			function o(e, s) {
				return s.x = e.x, s.y = e.y, s.z = e.z, s.w = e.w, s
			}
			function r(e, s) {
				var a = new t(e),
					r = s && s.state,
					n = function() {
						return (a.next() >>> 0) / 4294967296
					};
				return n.double = function() {
					do {
						var e = ((a.next() >>> 11) + (a.next() >>> 0) / 4294967296) / (1 << 21)
					} while (0 === e);
					return e
				}, n.int32 = a.next, n.quick = n, r && ("object" == typeof r && o(r, a), n.state = function() {
					return o(a, {})
				}), n
			}
			s && s.exports ? s.exports = r : a && a.amd ? a((function() {
				return r
			})) : this.xor128 = r
		}(0, e, !1)
	}(_e);
	var Le = {
		exports: {}
	};
	!function(e) {
		!function(e, s, a) {
			function t(e) {
				var s = this,
					a = "";
				s.next = function() {
					var e = s.x ^ s.x >>> 2;
					return s.x = s.y, s.y = s.z, s.z = s.w, s.w = s.v, (s.d = s.d + 362437 | 0) + (s.v = s.v ^ s.v << 4 ^ e ^ e << 1) | 0
				},
				s.x = 0,
				s.y = 0,
				s.z = 0,
				s.w = 0,
				s.v = 0,
				e === (0 | e) ? s.x = e : a += e;
				for (var t = 0; t < a.length + 64; t++)
					s.x ^= 0 | a.charCodeAt(t),
					t == a.length && (s.d = s.x << 10 ^ s.x >>> 4),
					s.next()
			}
			function o(e, s) {
				return s.x = e.x, s.y = e.y, s.z = e.z, s.w = e.w, s.v = e.v, s.d = e.d, s
			}
			function r(e, s) {
				var a = new t(e),
					r = s && s.state,
					n = function() {
						return (a.next() >>> 0) / 4294967296
					};
				return n.double = function() {
					do {
						var e = ((a.next() >>> 11) + (a.next() >>> 0) / 4294967296) / (1 << 21)
					} while (0 === e);
					return e
				}, n.int32 = a.next, n.quick = n, r && ("object" == typeof r && o(r, a), n.state = function() {
					return o(a, {})
				}), n
			}
			s && s.exports ? s.exports = r : a && a.amd ? a((function() {
				return r
			})) : this.xorwow = r
		}(0, e, !1)
	}(Le);
	var Ae = {
		exports: {}
	};
	!function(e) {
		!function(e, s, a) {
			function t(e) {
				var s = this;
				s.next = function() {
					var e,
						a,
						t = s.x,
						o = s.i;
					return e = t[o], a = (e ^= e >>> 7) ^ e << 24, a ^= (e = t[o + 1 & 7]) ^ e >>> 10, a ^= (e = t[o + 3 & 7]) ^ e >>> 3, a ^= (e = t[o + 4 & 7]) ^ e << 7, e = t[o + 7 & 7], a ^= (e ^= e << 13) ^ e << 9, t[o] = a, s.i = o + 1 & 7, a
				},
				function(e, s) {
					var a,
						t = [];
					if (s === (0 | s))
						t[0] = s;
					else
						for (s = "" + s, a = 0; a < s.length; ++a)
							t[7 & a] = t[7 & a] << 15 ^ s.charCodeAt(a) + t[a + 1 & 7] << 13;
					for (; t.length < 8;)
						t.push(0);
					for (a = 0; a < 8 && 0 === t[a]; ++a)
						;
					for (8 == a && (t[7] = -1), e.x = t, e.i = 0, a = 256; a > 0; --a)
						e.next()
				}(s, e)
			}
			function o(e, s) {
				return s.x = e.x.slice(), s.i = e.i, s
			}
			function r(e, s) {
				null == e && (e = +new Date);
				var a = new t(e),
					r = s && s.state,
					n = function() {
						return (a.next() >>> 0) / 4294967296
					};
				return n.double = function() {
					do {
						var e = ((a.next() >>> 11) + (a.next() >>> 0) / 4294967296) / (1 << 21)
					} while (0 === e);
					return e
				}, n.int32 = a.next, n.quick = n, r && (r.x && o(r, a), n.state = function() {
					return o(a, {})
				}), n
			}
			s && s.exports ? s.exports = r : a && a.amd ? a((function() {
				return r
			})) : this.xorshift7 = r
		}(0, e, !1)
	}(Ae);
	var He = {
		exports: {}
	};
	!function(e) {
		!function(e, s, a) {
			function t(e) {
				var s = this;
				s.next = function() {
					var e,
						a,
						t = s.w,
						o = s.X,
						r = s.i;
					return s.w = t = t + 1640531527 | 0, a = o[r + 34 & 127], e = o[r = r + 1 & 127], a ^= a << 13, e ^= e << 17, a ^= a >>> 15, e ^= e >>> 12, a = o[r] = a ^ e, s.i = r, a + (t ^ t >>> 16) | 0
				},
				function(e, s) {
					var a,
						t,
						o,
						r,
						n,
						i = [],
						l = 128;
					for (s === (0 | s) ? (t = s, s = null) : (s += "\0", t = 0, l = Math.max(l, s.length)), o = 0, r = -32; r < l; ++r)
						s && (t ^= s.charCodeAt((r + 32) % s.length)),
						0 === r && (n = t),
						t ^= t << 10,
						t ^= t >>> 15,
						t ^= t << 4,
						t ^= t >>> 13,
						r >= 0 && (n = n + 1640531527 | 0, o = 0 == (a = i[127 & r] ^= t + n) ? o + 1 : 0);
					for (o >= 128 && (i[127 & (s && s.length || 0)] = -1), o = 127, r = 512; r > 0; --r)
						t = i[o + 34 & 127],
						a = i[o = o + 1 & 127],
						t ^= t << 13,
						a ^= a << 17,
						t ^= t >>> 15,
						a ^= a >>> 12,
						i[o] = t ^ a;
					e.w = n,
					e.X = i,
					e.i = o
				}(s, e)
			}
			function o(e, s) {
				return s.i = e.i, s.w = e.w, s.X = e.X.slice(), s
			}
			function r(e, s) {
				null == e && (e = +new Date);
				var a = new t(e),
					r = s && s.state,
					n = function() {
						return (a.next() >>> 0) / 4294967296
					};
				return n.double = function() {
					do {
						var e = ((a.next() >>> 11) + (a.next() >>> 0) / 4294967296) / (1 << 21)
					} while (0 === e);
					return e
				}, n.int32 = a.next, n.quick = n, r && (r.X && o(r, a), n.state = function() {
					return o(a, {})
				}), n
			}
			s && s.exports ? s.exports = r : a && a.amd ? a((function() {
				return r
			})) : this.xor4096 = r
		}(0, e, !1)
	}(He);
	var Ie = {
		exports: {}
	};
	!function(e) {
		!function(e, s, a) {
			function t(e) {
				var s = this,
					a = "";
				s.next = function() {
					var e = s.b,
						a = s.c,
						t = s.d,
						o = s.a;
					return e = e << 25 ^ e >>> 7 ^ a, a = a - t | 0, t = t << 24 ^ t >>> 8 ^ o, o = o - e | 0, s.b = e = e << 20 ^ e >>> 12 ^ a, s.c = a = a - t | 0, s.d = t << 16 ^ a >>> 16 ^ o, s.a = o - e | 0
				},
				s.a = 0,
				s.b = 0,
				s.c = -1640531527,
				s.d = 1367130551,
				e === Math.floor(e) ? (s.a = e / 4294967296 | 0, s.b = 0 | e) : a += e;
				for (var t = 0; t < a.length + 20; t++)
					s.b ^= 0 | a.charCodeAt(t),
					s.next()
			}
			function o(e, s) {
				return s.a = e.a, s.b = e.b, s.c = e.c, s.d = e.d, s
			}
			function r(e, s) {
				var a = new t(e),
					r = s && s.state,
					n = function() {
						return (a.next() >>> 0) / 4294967296
					};
				return n.double = function() {
					do {
						var e = ((a.next() >>> 11) + (a.next() >>> 0) / 4294967296) / (1 << 21)
					} while (0 === e);
					return e
				}, n.int32 = a.next, n.quick = n, r && ("object" == typeof r && o(r, a), n.state = function() {
					return o(a, {})
				}), n
			}
			s && s.exports ? s.exports = r : a && a.amd ? a((function() {
				return r
			})) : this.tychei = r
		}(0, e, !1)
	}(Ie);
	var De = {
		exports: {}
	};
	!function(e) {
		!function(s, a, t) {
			var o,
				r = 256,
				n = t.pow(r, 6),
				i = t.pow(2, 52),
				l = 2 * i,
				u = 255;
			function c(e, u, c) {
				var g = [],
					f = h(m((u = 1 == u ? {
						entropy: !0
					} : u || {}).entropy ? [e, y(a)] : null == e ? function() {
						try {
							var e;
							return o && (e = o.randomBytes) ? e = e(r) : (e = new Uint8Array(r), (s.crypto || s.msCrypto).getRandomValues(e)), y(e)
						} catch (e) {
							var t = s.navigator,
								n = t && t.plugins;
							return [+new Date, s, n, s.screen, y(a)]
						}
					}() : e, 3), g),
					b = new d(g),
					k = function() {
						for (var e = b.g(6), s = n, a = 0; e < i;)
							e = (e + a) * r,
							s *= r,
							a = b.g(1);
						for (; e >= l;)
							e /= 2,
							s /= 2,
							a >>>= 1;
						return (e + a) / s
					};
				return k.int32 = function() {
					return 0 | b.g(4)
				}, k.quick = function() {
					return b.g(4) / 4294967296
				}, k.double = k, h(y(b.S), a), (u.pass || c || function(e, s, a, o) {
					return o && (o.S && p(o, b), e.state = function() {
						return p(b, {})
					}), a ? (t.random = e, s) : e
				})(k, f, "global" in u ? u.global : this == t, u.state)
			}
			function d(e) {
				var s,
					a = e.length,
					t = this,
					o = 0,
					n = t.i = t.j = 0,
					i = t.S = [];
				for (a || (e = [a++]); o < r;)
					i[o] = o++;
				for (o = 0; o < r; o++)
					i[o] = i[n = u & n + e[o % a] + (s = i[o])],
					i[n] = s;
				(t.g = function(e) {
					for (var s, a = 0, o = t.i, n = t.j, i = t.S; e--;)
						s = i[o = u & o + 1],
						a = a * r + i[u & (i[o] = i[n = u & n + s]) + (i[n] = s)];
					return t.i = o, t.j = n, a
				})(r)
			}
			function p(e, s) {
				return s.i = e.i, s.j = e.j, s.S = e.S.slice(), s
			}
			function m(e, s) {
				var a,
					t = [],
					o = typeof e;
				if (s && "object" == o)
					for (a in e)
						try {
							t.push(m(e[a], s - 1))
						} catch (e) {}
				return t.length ? t : "string" == o ? e : e + "\0"
			}
			function h(e, s) {
				for (var a, t = e + "", o = 0; o < t.length;)
					s[u & o] = u & (a ^= 19 * s[u & o]) + t.charCodeAt(o++);
				return y(s)
			}
			function y(e) {
				return String.fromCharCode.apply(0, e)
			}
			if (h(t.random(), a), e.exports) {
				e.exports = c;
				try {
					o = require("crypto")
				} catch (e) {}
			} else
				t.seedrandom = c
		}("undefined" != typeof self ? self : Oe, [], Math)
	}(De);
	var Re = Ee.exports,
		Ue = _e.exports,
		Be = Le.exports,
		We = Ae.exports,
		Ge = He.exports,
		Pe = Ie.exports,
		Je = De.exports;
	Je.alea = Re,
	Je.xor128 = Ue,
	Je.xorwow = Be,
	Je.xorshift7 = We,
	Je.xor4096 = Ge,
	Je.tychei = Pe;
	var Ye,
		Ve,
		Xe = Je;
	!function(e) {
		e[e.daily = 0] = "daily",
		e[e.hourly = 1] = "hourly",
		e[e.infinite = 2] = "infinite"
	}(Ye || (Ye = {})),
	function(e) {
		e[e.SECOND = 1e3] = "SECOND",
		e[e.MINUTE = 6e4] = "MINUTE",
		e[e.HOUR = 36e5] = "HOUR",
		e[e.DAY = 864e5] = "DAY"
	}(Ve || (Ve = {}));
	const Fe = {
			words: ["apple"],
			valid: ["aahed"]
		},
		Ke = Object.assign(Object.assign({}, Fe), {
			contains: e => Fe.words.includes(e) || Fe.valid.includes(e)
		});
	class Ze {
		constructor()
		{
			this.notSet = new Set
		}
		not(e)
		{
			this.notSet.add(e)
		}
	}
	class Qe {
		constructor()
		{
			this.notSet = new Set,
			this.letterCounts = new Map,
			this.word = [];
			for (let e = 0; e < 5; ++e)
				this.word.push(new Ze)
		}
		confirmCount(e)
		{
			let s = this.letterCounts.get(e);
			s ? s[1] = !0 : this.not(e)
		}
		countConfirmed(e)
		{
			const s = this.letterCounts.get(e);
			return !!s && s[1]
		}
		setCount(e, s)
		{
			let a = this.letterCounts.get(e);
			a ? a[0] = s : this.letterCounts.set(e, [s, !1])
		}
		incrementCount(e)
		{
			++this.letterCounts.get(e)[0]
		}
		not(e)
		{
			this.notSet.add(e)
		}
		inGlobalNotList(e)
		{
			return this.notSet.has(e)
		}
		lettersNotAt(e)
		{
			return new Set([...this.notSet, ...this.word[e].notSet])
		}
	}
	function es(e, s) {
		return e.reduce(((e, a) => a === s ? e + 1 : e), 0)
	}
	const ss = ["qwertyuiop", "asdfghjkl", "zxcvbnm"];
	function as(e) {
		const s = Date.now();
		switch (e) {
		case Ye.daily:
			return Date.UTC(1970, 0, 1 + Math.floor((s - (new Date).getTimezoneOffset() * Ve.MINUTE) / Ve.DAY));
		case Ye.hourly:
			return s - s % Ve.HOUR;
		case Ye.infinite:
			return s - s % Ve.SECOND
		}
	}
	const ts = {
		default: Ye.daily,
		modes: [{
			name: "Daily",
			unit: Ve.DAY,
			start: 16423704e5,
			seed: as(Ye.daily),
			historical: !1,
			streak: !0,
			useTimeZone: !0
		}, {
			name: "Hourly",
			unit: Ve.HOUR,
			start: 16425288e5,
			seed: as(Ye.hourly),
			historical: !1,
			icon: "m50,7h100v33c0,40 -35,40 -35,60c0,20 35,20 35,60v33h-100v-33c0,-40 35,-40 35,-60c0,-20 -35,-20 -35,-60z",
			streak: !0
		}, {
			name: "Infinite",
			unit: Ve.SECOND,
			start: 16424286e5,
			seed: as(Ye.infinite),
			historical: !1,
			icon: "m7,100c0,-50 68,-50 93,0c25,50 93,50 93,0c0,-50 -68,-50 -93,0c-25,50 -93,50 -93,0z"
		}]
	};
	function os(e) {
		return Math.round((ts.modes[e].seed - ts.modes[e].start) / ts.modes[e].unit) + 1
	}
	function rs(e, s, a) {
		const t = Xe(`${a}`);
		return Math.floor(e + (s - e) * t())
	}
	const ns = 200,
		is = ["Genius", "Magnificent", "Impressive", "Splendid", "Great", "Phew"];
	function ls(e) {
		return {
			active: !0,
			guesses: 0,
			time: ts.modes[e].seed,
			wordNumber: os(e),
			validHard: !0,
			board: {
				words: Array(6).fill(""),
				state: Array.from({
					length: 6
				}, (() => Array(5).fill("🔳")))
			}
		}
	}
	function us() {
		return {
			hard: new Array(ts.modes.length).map((() => !1)),
			dark: !0,
			colorblind: !1,
			tutorial: 3
		}
	}
	function cs(e) {
		return e.useTimeZone ? e.unit - (Date.now() - (e.seed + (new Date).getTimezoneOffset() * Ve.MINUTE)) : e.unit - (Date.now() - e.seed)
	}
	function ds(e) {
		return !(e.active || e.guesses > 0 && e.board.state[e.guesses - 1].join("") === "🟩".repeat(5))
	}
	function ps(e) {
		const s = e - 1;
		return s * s * s + 1
	}
	function ms(e, {delay: a=0, duration: t=400, easing: o=s}={}) {
		const r = +getComputedStyle(e).opacity;
		return {
			delay: a,
			duration: t,
			easing: o,
			css: e => "opacity: " + e * r
		}
	}
	function hs(e, {delay: s=0, duration: a=400, easing: t=ps, start: o=0, opacity: r=0}={}) {
		const n = getComputedStyle(e),
			i = +n.opacity,
			l = "none" === n.transform ? "" : n.transform,
			u = 1 - o,
			c = i * (1 - r);
		return {
			delay: s,
			duration: a,
			easing: t,
			css: (e, s) => `\n\t\t\ttransform: ${l} scale(${1 - u * s});\n\t\t\topacity: ${i - c * s}\n\t\t`
		}
	}
	const ys = [];
	function gs(s, a=e) {
		let t;
		const o = new Set;
		function r(e) {
			if (n(s, e) && (s = e, t)) {
				const e = !ys.length;
				for (const e of o)
					e[1](),
					ys.push(e, s);
				if (e) {
					for (let e = 0; e < ys.length; e += 2)
						ys[e][0](ys[e + 1]);
					ys.length = 0
				}
			}
		}
		return {
			set: r,
			update: function(e) {
				r(e(s))
			},
			subscribe: function(n, i=e) {
				const l = [n, i];
				return o.add(l), 1 === o.size && (t = a(r) || e), n(s), () => {
					o.delete(l),
					0 === o.size && (t(), t = null)
				}
			}
		}
	}
	const fs = gs(),
		bs = gs({
			a: "🔳",
			b: "🔳",
			c: "🔳",
			d: "🔳",
			e: "🔳",
			f: "🔳",
			g: "🔳",
			h: "🔳",
			i: "🔳",
			j: "🔳",
			k: "🔳",
			l: "🔳",
			m: "🔳",
			n: "🔳",
			o: "🔳",
			p: "🔳",
			q: "🔳",
			r: "🔳",
			s: "🔳",
			t: "🔳",
			u: "🔳",
			v: "🔳",
			w: "🔳",
			x: "🔳",
			y: "🔳",
			z: "🔳"
		}),
		ks = gs(us());
	function ws(e) {
		let s,
			a,
			t,
			o;
		const n = e[2].default,
			i = l(n, e, e[1], null);
		return {
			c() {
				s = N("svg"),
				i && i.c(),
				A(s, "xmlns", "http://www.w3.org/2000/svg"),
				A(s, "viewBox", "0 0 24 24"),
				A(s, "class", "svelte-17ud64h")
			},
			m(n, l) {
				j(n, s, l),
				i && i.m(s, null),
				a = !0,
				t || (o = E(s, "click", (function() {
					r(e[0]) && e[0].apply(this, arguments)
				})), t = !0)
			},
			p(s, [t]) {
				e = s,
				i && i.p && (!a || 2 & t) && d(i, n, e, e[1], a ? c(n, e[1], t, null) : p(e[1]), null)
			},
			i(e) {
				a || (ke(i, e), a = !0)
			},
			o(e) {
				we(i, e),
				a = !1
			},
			d(e) {
				e && q(s),
				i && i.d(e),
				t = !1,
				o()
			}
		}
	}
	function vs(e, s, a) {
		let {$$slots: t={}, $$scope: o} = s,
			{onClick: r=(() => {})} = s;
		return e.$$set = e => {
			"onClick" in e && a(0, r = e.onClick),
			"$$scope" in e && a(1, o = e.$$scope)
		}, [r, o, t]
	}
	class $s extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, vs, ws, n, {
				onClick: 0
			})
		}
	}
	function xs(s) {
		let a;
		return {
			c() {
				a = N("path"),
				A(a, "d", "M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function zs(e) {
		let s,
			a;
		return s = new $s({
			props: {
				onClick: e[7],
				$$slots: {
					default: [js]
				},
				$$scope: {
					ctx: e
				}
			}
		}), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				8192 & a && (t.$$scope = {
					dirty: a,
					ctx: e
				}),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function js(s) {
		let a,
			t,
			o;
		return {
			c() {
				a = N("path"),
				A(a, "d", "M4.609 12c0-4.082 3.309-7.391 7.391-7.391a7.39 7.39 0 0 1 6.523 3.912l-1.653 1.567H22v-5.13l-1.572 1.659C18.652 3.841 15.542 2 12 2 6.477 2 2 6.477 2 12s4.477 10 10 10c4.589 0 8.453-3.09 9.631-7.301l-2.512-.703c-.871 3.113-3.73 5.395-7.119 5.395-4.082 0-7.391-3.309-7.391-7.391z")
			},
			m(e, s) {
				j(e, a, s),
				o = !0
			},
			p: e,
			i(e) {
				o || (ne((() => {
					t || (t = $e(a, ms, {
						duration: 200
					}, !0)),
					t.run(1)
				})), o = !0)
			},
			o(e) {
				t || (t = $e(a, ms, {
					duration: 200
				}, !1)),
				t.run(0),
				o = !1
			},
			d(e) {
				e && q(a),
				e && t && t.end()
			}
		}
	}
	function qs(e) {
		let s,
			a;
		return s = new $s({
			props: {
				onClick: e[10],
				$$slots: {
					default: [Cs]
				},
				$$scope: {
					ctx: e
				}
			}
		}), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				8192 & a && (t.$$scope = {
					dirty: a,
					ctx: e
				}),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function Cs(s) {
		let a,
			t,
			o;
		return {
			c() {
				a = N("path"),
				A(a, "d", "M16,11V3H8v6H2v12h20V11H16z M10,5h4v14h-4V5z M4,11h4v8H4V11z M20,19h-4v-6h4V19z")
			},
			m(e, s) {
				j(e, a, s),
				o = !0
			},
			p: e,
			i(e) {
				o || (ne((() => {
					t || (t = $e(a, ms, {
						duration: 200
					}, !0)),
					t.run(1)
				})), o = !0)
			},
			o(e) {
				t || (t = $e(a, ms, {
					duration: 200
				}, !1)),
				t.run(0),
				o = !1
			},
			d(e) {
				e && q(a),
				e && t && t.end()
			}
		}
	}
	function Ss(s) {
		let a;
		return {
			c() {
				a = N("path"),
				A(a, "d", "M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Ns(s) {
		let a,
			t,
			o,
			r,
			n;
		return {
			c() {
				a = S("div"),
				a.innerHTML = 'Tap WORDLE+ to change game mode\n\t\t\t<span class="ok">OK</span>',
				A(a, "class", "tutorial")
			},
			m(e, t) {
				j(e, a, t),
				o = !0,
				r || (n = E(a, "click", s[12]), r = !0)
			},
			p: e,
			i(e) {
				o || (ne((() => {
					t || (t = $e(a, hs, {}, !0)),
					t.run(1)
				})), o = !0)
			},
			o(e) {
				t || (t = $e(a, hs, {}, !1)),
				t.run(0),
				o = !1
			},
			d(e) {
				e && q(a),
				e && t && t.end(),
				r = !1,
				n()
			}
		}
	}
	function Ms(e) {
		let s,
			a,
			t,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m,
			h,
			y;
		t = new $s({
			props: {
				onClick: e[6],
				$$slots: {
					default: [xs]
				},
				$$scope: {
					ctx: e
				}
			}
		});
		let g = e[0] && zs(e),
			f = e[1] && qs(e);
		d = new $s({
			props: {
				onClick: e[11],
				$$slots: {
					default: [Ss]
				},
				$$scope: {
					ctx: e
				}
			}
		});
		let b = e[2] && Ns(e);
		return {
			c() {
				s = S("header"),
				a = S("div"),
				qe(t.$$.fragment),
				r = T(),
				g && g.c(),
				n = T(),
				i = S("h1"),
				i.textContent = "wordle+",
				l = T(),
				u = S("div"),
				f && f.c(),
				c = T(),
				qe(d.$$.fragment),
				p = T(),
				b && b.c(),
				A(a, "class", "icons svelte-2k6r7n"),
				A(i, "class", "svelte-2k6r7n"),
				A(u, "class", "icons svelte-2k6r7n"),
				A(s, "class", "svelte-2k6r7n")
			},
			m(o, k) {
				j(o, s, k),
				$(s, a),
				Ce(t, a, null),
				$(a, r),
				g && g.m(a, null),
				$(s, n),
				$(s, i),
				$(s, l),
				$(s, u),
				f && f.m(u, null),
				$(u, c),
				Ce(d, u, null),
				$(s, p),
				b && b.m(s, null),
				m = !0,
				h || (y = [E(i, "click", L(e[8])), E(i, "contextmenu", L(_(e[9])))], h = !0)
			},
			p(e, [o]) {
				const r = {};
				8192 & o && (r.$$scope = {
					dirty: o,
					ctx: e
				}),
				t.$set(r),
				e[0] ? g ? (g.p(e, o), 1 & o && ke(g, 1)) : (g = zs(e), g.c(), ke(g, 1), g.m(a, null)) : g && (fe(), we(g, 1, 1, (() => {
					g = null
				})), be()),
				e[1] ? f ? (f.p(e, o), 2 & o && ke(f, 1)) : (f = qs(e), f.c(), ke(f, 1), f.m(u, c)) : f && (fe(), we(f, 1, 1, (() => {
					f = null
				})), be());
				const n = {};
				8192 & o && (n.$$scope = {
					dirty: o,
					ctx: e
				}),
				d.$set(n),
				e[2] ? b ? (b.p(e, o), 4 & o && ke(b, 1)) : (b = Ns(e), b.c(), ke(b, 1), b.m(s, null)) : b && (fe(), we(b, 1, 1, (() => {
					b = null
				})), be())
			},
			i(e) {
				m || (ke(t.$$.fragment, e), ke(g), ke(f), ke(d.$$.fragment, e), ke(b), m = !0)
			},
			o(e) {
				we(t.$$.fragment, e),
				we(g),
				we(f),
				we(d.$$.fragment, e),
				we(b),
				m = !1
			},
			d(e) {
				e && q(s),
				Se(t),
				g && g.d(),
				f && f.d(),
				Se(d),
				b && b.d(),
				h = !1,
				o(y)
			}
		}
	}
	function Ts(e, s, a) {
		let t;
		i(e, fs, (e => a(4, t = e)));
		let {showStats: o} = s,
			{tutorial: r} = s,
			{showRefresh: n} = s,
			{toaster: l=Q("toaster")} = s;
		const u = K();
		fs.subscribe((e => {
			cs(ts.modes[e]) > 0 && a(0, n = !1)
		}));
		return e.$$set = e => {
			"showStats" in e && a(1, o = e.showStats),
			"tutorial" in e && a(2, r = e.tutorial),
			"showRefresh" in e && a(0, n = e.showRefresh),
			"toaster" in e && a(3, l = e.toaster)
		}, [n, o, r, l, t, u, () => u("tutorial"), () => u("reload"), () => {
			y(fs, t = (t + 1) % ts.modes.length, t),
			l.pop(ts.modes[t].name)
		}, () => {
			y(fs, t = (t - 1 + ts.modes.length) % ts.modes.length, t),
			l.pop(ts.modes[t].name)
		}, () => u("stats"), () => u("settings"), () => u("closeTutPopUp")]
	}
	class Os extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Ts, Ms, n, {
				showStats: 1,
				tutorial: 2,
				showRefresh: 0,
				toaster: 3
			})
		}
	}
	function Es(s) {
		let a,
			t,
			o,
			r,
			n,
			i,
			l;
		return {
			c() {
				a = S("div"),
				t = S("div"),
				o = M(s[0]),
				r = T(),
				n = S("div"),
				i = M(s[0]),
				A(t, "class", "front svelte-frmspd"),
				A(n, "class", "back svelte-frmspd"),
				A(a, "data-animation", s[5]),
				A(a, "class", l = "tile " + s[1] + " " + s[3] + " svelte-frmspd"),
				I(a, "transition-delay", s[2] * ns + "ms"),
				R(a, "value", s[0]),
				R(a, "pop", s[4])
			},
			m(e, s) {
				j(e, a, s),
				$(a, t),
				$(t, o),
				$(a, r),
				$(a, n),
				$(n, i)
			},
			p(e, [s]) {
				1 & s && H(o, e[0]),
				1 & s && H(i, e[0]),
				32 & s && A(a, "data-animation", e[5]),
				10 & s && l !== (l = "tile " + e[1] + " " + e[3] + " svelte-frmspd") && A(a, "class", l),
				4 & s && I(a, "transition-delay", e[2] * ns + "ms"),
				11 & s && R(a, "value", e[0]),
				26 & s && R(a, "pop", e[4])
			},
			i: e,
			o: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function _s(e, s, a) {
		let t,
			{value: o=""} = s,
			{state: r} = s,
			{position: n=0} = s;
		let i = !1,
			l = "";
		const u = fs.subscribe((() => {
			a(5, l = ""),
			a(3, t = "🔳"),
			setTimeout((() => a(3, t = "")), 10)
		}));
		return setTimeout((() => a(4, i = !0)), 200), F(u), e.$$set = e => {
			"value" in e && a(0, o = e.value),
			"state" in e && a(1, r = e.state),
			"position" in e && a(2, n = e.position)
		}, e.$$.update = () => {
			1 & e.$$.dirty && !o && a(5, l = "")
		}, [o, r, n, t, i, l, function() {
			setTimeout((() => a(5, l = "bounce")), (6 + n) * ns)
		}]
	}
	class Ls extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, _s, Es, n, {
				value: 0,
				state: 1,
				position: 2,
				bounce: 6
			})
		}
		get bounce()
		{
			return this.$$.ctx[6]
		}
	}
	function As(e, s, a) {
		const t = e.slice();
		return t[13] = s[a], t[14] = s, t[15] = a, t
	}
	function Hs(e) {
		let s,
			a,
			t = e[15];
		const o = () => e[9](s, t),
			r = () => e[9](null, t);
		let n = {
			state: e[3][e[15]],
			value: e[2].charAt(e[15]),
			position: e[15]
		};
		return s = new Ls({
			props: n
		}), o(), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				t !== e[15] && (r(), t = e[15], o());
				const n = {};
				8 & a && (n.state = e[3][e[15]]),
				4 & a && (n.value = e[2].charAt(e[15])),
				s.$set(n)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				r(),
				Se(s, e)
			}
		}
	}
	function Is(e) {
		let s,
			a,
			t,
			r,
			n = Array(5),
			i = [];
		for (let s = 0; s < n.length; s += 1)
			i[s] = Hs(As(e, n, s));
		const l = e => we(i[e], 1, 1, (() => {
			i[e] = null
		}));
		return {
			c() {
				s = S("div");
				for (let e = 0; e < i.length; e += 1)
					i[e].c();
				A(s, "class", "board-row svelte-ssibky"),
				A(s, "data-animation", e[4]),
				R(s, "complete", e[0] > e[1])
			},
			m(o, n) {
				j(o, s, n);
				for (let e = 0; e < i.length; e += 1)
					i[e].m(s, null);
				a = !0,
				t || (r = [E(s, "contextmenu", _(e[10])), E(s, "dblclick", _(e[11])), E(s, "animationend", e[12])], t = !0)
			},
			p(e, [t]) {
				if (44 & t) {
					let a;
					for (n = Array(5), a = 0; a < n.length; a += 1) {
						const o = As(e, n, a);
						i[a] ? (i[a].p(o, t), ke(i[a], 1)) : (i[a] = Hs(o), i[a].c(), ke(i[a], 1), i[a].m(s, null))
					}
					for (fe(), a = n.length; a < i.length; a += 1)
						l(a);
					be()
				}
				(!a || 16 & t) && A(s, "data-animation", e[4]),
				3 & t && R(s, "complete", e[0] > e[1])
			},
			i(e) {
				if (!a) {
					for (let e = 0; e < n.length; e += 1)
						ke(i[e]);
					a = !0
				}
			},
			o(e) {
				i = i.filter(Boolean);
				for (let e = 0; e < i.length; e += 1)
					we(i[e]);
				a = !1
			},
			d(e) {
				e && q(s),
				C(i, e),
				t = !1,
				o(r)
			}
		}
	}
	function Ds(e, s, a) {
		let {guesses: t} = s,
			{num: o} = s,
			{value: r=""} = s,
			{state: n} = s;
		const i = K();
		let l = "",
			u = [];
		return e.$$set = e => {
			"guesses" in e && a(0, t = e.guesses),
			"num" in e && a(1, o = e.num),
			"value" in e && a(2, r = e.value),
			"state" in e && a(3, n = e.state)
		}, [t, o, r, n, l, u, i, function() {
			a(4, l = "shake")
		}, function() {
			u.forEach((e => e.bounce()))
		}, function(e, s) {
			se[e ? "unshift" : "push"]((() => {
				u[s] = e,
				a(5, u)
			}))
		}, e => i("ctx", {
			x: e.clientX,
			y: e.clientY
		}), e => i("ctx", {
			x: e.clientX,
			y: e.clientY
		}), () => a(4, l = "")]
	}
	class Rs extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Ds, Is, n, {
				guesses: 0,
				num: 1,
				value: 2,
				state: 3,
				shake: 7,
				bounce: 8
			})
		}
		get shake()
		{
			return this.$$.ctx[7]
		}
		get bounce()
		{
			return this.$$.ctx[8]
		}
	}
	function Us(e, s, a) {
		const t = e.slice();
		return t[3] = s[a], t
	}
	function Bs(e) {
		let s,
			a,
			t,
			o,
			r;
		return {
			c() {
				s = S("div"),
				a = M("Your word was "),
				t = S("strong"),
				o = M(e[0]),
				r = M(". (failed to fetch definition)")
			},
			m(e, n) {
				j(e, s, n),
				$(s, a),
				$(s, t),
				$(t, o),
				$(s, r)
			},
			p(e, s) {
				1 & s && H(o, e[0])
			},
			d(e) {
				e && q(s)
			}
		}
	}
	function Ws(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u = e[2].meanings[0].partOfSpeech + "",
			c = e[0] !== e[2].word && Gs(e),
			d = e[2].meanings[0].definitions.slice(0, 1 + e[1] - (e[0] !== e[2].word ? 1 : 0)),
			p = [];
		for (let s = 0; s < d.length; s += 1)
			p[s] = Ps(Us(e, d, s));
		return {
			c() {
				s = S("h2"),
				a = M(e[0]),
				t = T(),
				o = S("em"),
				r = M(u),
				n = T(),
				i = S("ol"),
				c && c.c(),
				l = T();
				for (let e = 0; e < p.length; e += 1)
					p[e].c();
				A(s, "class", "svelte-1cpaagx"),
				A(i, "class", "svelte-1cpaagx")
			},
			m(e, u) {
				j(e, s, u),
				$(s, a),
				j(e, t, u),
				j(e, o, u),
				$(o, r),
				j(e, n, u),
				j(e, i, u),
				c && c.m(i, null),
				$(i, l);
				for (let e = 0; e < p.length; e += 1)
					p[e].m(i, null)
			},
			p(e, s) {
				if (1 & s && H(a, e[0]), 1 & s && u !== (u = e[2].meanings[0].partOfSpeech + "") && H(r, u), e[0] !== e[2].word ? c ? c.p(e, s) : (c = Gs(e), c.c(), c.m(i, l)) : c && (c.d(1), c = null), 3 & s) {
					let a;
					for (d = e[2].meanings[0].definitions.slice(0, 1 + e[1] - (e[0] !== e[2].word ? 1 : 0)), a = 0; a < d.length; a += 1) {
						const t = Us(e, d, a);
						p[a] ? p[a].p(t, s) : (p[a] = Ps(t), p[a].c(), p[a].m(i, null))
					}
					for (; a < p.length; a += 1)
						p[a].d(1);
					p.length = d.length
				}
			},
			d(e) {
				e && q(s),
				e && q(t),
				e && q(o),
				e && q(n),
				e && q(i),
				c && c.d(),
				C(p, e)
			}
		}
	}
	function Gs(e) {
		let s,
			a,
			t,
			o,
			r = e[2].word + "";
		return {
			c() {
				s = S("li"),
				a = M("variant of "),
				t = M(r),
				o = M("."),
				A(s, "class", "svelte-1cpaagx")
			},
			m(e, r) {
				j(e, s, r),
				$(s, a),
				$(s, t),
				$(s, o)
			},
			p(e, s) {
				1 & s && r !== (r = e[2].word + "") && H(t, r)
			},
			d(e) {
				e && q(s)
			}
		}
	}
	function Ps(e) {
		let s,
			a,
			t = e[3].definition + "";
		return {
			c() {
				s = S("li"),
				a = M(t),
				A(s, "class", "svelte-1cpaagx")
			},
			m(e, t) {
				j(e, s, t),
				$(s, a)
			},
			p(e, s) {
				3 & s && t !== (t = e[3].definition + "") && H(a, t)
			},
			d(e) {
				e && q(s)
			}
		}
	}
	function Js(s) {
		let a;
		return {
			c() {
				a = S("h4"),
				a.textContent = "Fetching definition..."
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Ys(s) {
		let a,
			t,
			o = {
				ctx: s,
				current: null,
				token: null,
				hasCatch: !0,
				pending: Js,
				then: Ws,
				catch: Bs,
				value: 2
			};
		return xe(t = Xs(s[0]), o), {
			c() {
				a = S("div"),
				o.block.c(),
				A(a, "class", "def")
			},
			m(e, s) {
				j(e, a, s),
				o.block.m(a, o.anchor = null),
				o.mount = () => a,
				o.anchor = null
			},
			p(e, [a]) {
				s = e,
				o.ctx = s,
				1 & a && t !== (t = Xs(s[0])) && xe(t, o) || function(e, s, a) {
					const t = s.slice(),
						{resolved: o} = e;
					e.current === e.then && (t[e.value] = o),
					e.current === e.catch && (t[e.error] = o),
					e.block.p(t, a)
				}(o, s, a)
			},
			i: e,
			o: e,
			d(e) {
				e && q(a),
				o.block.d(),
				o.token = null,
				o = null
			}
		}
	}
	const Vs = new Map;
	async function Xs(e) {
		if (!Vs.has(e)) {
			const s = await fetch(`https://api.dictionaryapi.dev/api/v2/entries/en/${e}`, {
				mode: "cors"
			});
			if (!s.ok)
				throw new Error("Failed to fetch definition");
			Vs.set(e, (await s.json())[0])
		}
		return Vs.get(e)
	}
	function Fs(e, s, a) {
		let {word: t} = s,
			{alternates: o=9} = s;
		return e.$$set = e => {
			"word" in e && a(0, t = e.word),
			"alternates" in e && a(1, o = e.alternates)
		}, [t, o]
	}
	class Ks extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Fs, Ys, n, {
				word: 0,
				alternates: 1
			})
		}
	}
	function Zs(s) {
		let a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m,
			h,
			y,
			g,
			f,
			b = s[3] > 1 ? "are" : "is",
			k = s[3] > 1 ? "s" : "",
			w = s[4] > 1 ? "es" : "";
		return {
			c() {
				a = S("div"),
				t = M("Considering all hints, there "),
				o = M(b),
				r = M(":\n\t\t\t"),
				n = S("br"),
				i = S("br"),
				l = T(),
				u = M(s[3]),
				c = M(" possible answer"),
				d = M(k),
				p = T(),
				m = S("br"),
				h = T(),
				y = M(s[4]),
				g = M(" valid guess"),
				f = M(w)
			},
			m(e, s) {
				j(e, a, s),
				$(a, t),
				$(a, o),
				$(a, r),
				$(a, n),
				$(a, i),
				$(a, l),
				$(a, u),
				$(a, c),
				$(a, d),
				$(a, p),
				$(a, m),
				$(a, h),
				$(a, y),
				$(a, g),
				$(a, f)
			},
			p(e, s) {
				8 & s && b !== (b = e[3] > 1 ? "are" : "is") && H(o, b),
				8 & s && H(u, e[3]),
				8 & s && k !== (k = e[3] > 1 ? "s" : "") && H(d, k),
				16 & s && H(y, e[4]),
				16 & s && w !== (w = e[4] > 1 ? "es" : "") && H(f, w)
			},
			i: e,
			o: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Qs(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m,
			h,
			y,
			g,
			f,
			b = e[3] > 1 ? "s" : "",
			k = e[4] > 1 ? "es" : "";
		return g = new Ks({
			props: {
				word: e[2],
				alternates: 1
			}
		}), {
			c() {
				s = S("div"),
				a = M("Considering all hints, this row had:\n\t\t\t"),
				t = S("br"),
				o = S("br"),
				r = T(),
				n = M(e[3]),
				i = M(" possible answer"),
				l = M(b),
				u = T(),
				c = S("br"),
				d = T(),
				p = M(e[4]),
				m = M(" valid guess"),
				h = M(k),
				y = T(),
				qe(g.$$.fragment)
			},
			m(e, b) {
				j(e, s, b),
				$(s, a),
				$(s, t),
				$(s, o),
				$(s, r),
				$(s, n),
				$(s, i),
				$(s, l),
				$(s, u),
				$(s, c),
				$(s, d),
				$(s, p),
				$(s, m),
				$(s, h),
				j(e, y, b),
				Ce(g, e, b),
				f = !0
			},
			p(e, s) {
				(!f || 8 & s) && H(n, e[3]),
				(!f || 8 & s) && b !== (b = e[3] > 1 ? "s" : "") && H(l, b),
				(!f || 16 & s) && H(p, e[4]),
				(!f || 16 & s) && k !== (k = e[4] > 1 ? "es" : "") && H(h, k);
				const a = {};
				4 & s && (a.word = e[2]),
				g.$set(a)
			},
			i(e) {
				f || (ke(g.$$.fragment, e), f = !0)
			},
			o(e) {
				we(g.$$.fragment, e),
				f = !1
			},
			d(e) {
				e && q(s),
				e && q(y),
				Se(g, e)
			}
		}
	}
	function ea(e) {
		let s,
			a,
			t,
			o;
		const r = [Qs, Zs],
			n = [];
		function i(e, s) {
			return "" !== e[2] ? 0 : 1
		}
		return a = i(e), t = n[a] = r[a](e), {
			c() {
				s = S("div"),
				t.c(),
				A(s, "class", "ctx-menu svelte-uw0qlf"),
				I(s, "top", e[1] + "px"),
				I(s, "left", e[0] + "px")
			},
			m(e, t) {
				j(e, s, t),
				n[a].m(s, null),
				o = !0
			},
			p(e, [l]) {
				let u = a;
				a = i(e),
				a === u ? n[a].p(e, l) : (fe(), we(n[u], 1, 1, (() => {
					n[u] = null
				})), be(), t = n[a], t ? t.p(e, l) : (t = n[a] = r[a](e), t.c()), ke(t, 1), t.m(s, null)),
				(!o || 2 & l) && I(s, "top", e[1] + "px"),
				(!o || 1 & l) && I(s, "left", e[0] + "px")
			},
			i(e) {
				o || (ke(t), o = !0)
			},
			o(e) {
				we(t),
				o = !1
			},
			d(e) {
				e && q(s),
				n[a].d()
			}
		}
	}
	function sa(e, s, a) {
		let {x: t=0} = s,
			{y: o=0} = s,
			{word: r=""} = s,
			{pAns: n} = s,
			{pSols: i} = s;
		const l = parseInt(getComputedStyle(document.body).getPropertyValue("--game-width")) / 2;
		return e.$$set = e => {
			"x" in e && a(0, t = e.x),
			"y" in e && a(1, o = e.y),
			"word" in e && a(2, r = e.word),
			"pAns" in e && a(3, n = e.pAns),
			"pSols" in e && a(4, i = e.pSols)
		}, e.$$.update = () => {
			1 & e.$$.dirty && a(0, t = window.innerWidth - t < l ? window.innerWidth - l : t)
		}, [t, o, r, n, i]
	}
	class aa extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, sa, ea, n, {
				x: 0,
				y: 1,
				word: 2,
				pAns: 3,
				pSols: 4
			})
		}
	}
	function ta(e, s, a) {
		const t = e.slice();
		return t[21] = s[a], t[22] = s, t[23] = a, t
	}
	function oa(e) {
		let s,
			a;
		return s = new aa({
			props: {
				pAns: e[7],
				pSols: e[8],
				x: e[9],
				y: e[10],
				word: e[11]
			}
		}), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				128 & a && (t.pAns = e[7]),
				256 & a && (t.pSols = e[8]),
				512 & a && (t.x = e[9]),
				1024 & a && (t.y = e[10]),
				2048 & a && (t.word = e[11]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function ra(e) {
		let s,
			a,
			t,
			o = e[23];
		const r = () => e[17](s, o),
			n = () => e[17](null, o);
		function i(s) {
			e[18](s, e[23])
		}
		let l = {
			num: e[23],
			guesses: e[2],
			state: e[1].state[e[23]]
		};
		return void 0 !== e[0][e[23]] && (l.value = e[0][e[23]]), s = new Rs({
			props: l
		}), r(), se.push((() => je(s, "value", i))), s.$on("ctx", (function(...s) {
			return e[19](e[23], ...s)
		})), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, a) {
				Ce(s, e, a),
				t = !0
			},
			p(t, i) {
				o !== (e = t)[23] && (n(), o = e[23], r());
				const l = {};
				4 & i && (l.guesses = e[2]),
				2 & i && (l.state = e[1].state[e[23]]),
				!a && 1 & i && (a = !0, l.value = e[0][e[23]], ie((() => a = !1))),
				s.$set(l)
			},
			i(e) {
				t || (ke(s.$$.fragment, e), t = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				t = !1
			},
			d(e) {
				n(),
				Se(s, e)
			}
		}
	}
	function na(e) {
		let s,
			a;
		return {
			c() {
				s = N("svg"),
				a = N("path"),
				A(a, "d", e[3]),
				A(a, "stroke-width", "14"),
				A(a, "class", "svelte-gexn6m"),
				A(s, "xmlns", "http://www.w3.org/2000/svg"),
				A(s, "viewBox", "0 0 200 200"),
				A(s, "fill", "none"),
				A(s, "class", "svelte-gexn6m")
			},
			m(e, t) {
				j(e, s, t),
				$(s, a)
			},
			p(e, s) {
				8 & s && A(a, "d", e[3])
			},
			d(e) {
				e && q(s)
			}
		}
	}
	function ia(s) {
		let a,
			t,
			o,
			r,
			n;
		return {
			c() {
				a = S("div"),
				a.innerHTML = 'double tap (right click) a row to see a word&#39;s definition, or how many words could be\n\t\t\tplayed there\n\t\t\t<span class="ok">OK</span>',
				A(a, "class", "tutorial svelte-gexn6m")
			},
			m(e, t) {
				j(e, a, t),
				o = !0,
				r || (n = E(a, "click", s[20]), r = !0)
			},
			p: e,
			i(e) {
				o || (ne((() => {
					t || (t = $e(a, hs, {}, !0)),
					t.run(1)
				})), o = !0)
			},
			o(e) {
				t || (t = $e(a, hs, {}, !1)),
				t.run(0),
				o = !1
			},
			d(e) {
				e && q(a),
				e && t && t.end(),
				r = !1,
				n()
			}
		}
	}
	function la(e) {
		let s,
			a,
			t,
			o,
			r,
			n = e[6] && oa(e),
			i = e[0],
			l = [];
		for (let s = 0; s < i.length; s += 1)
			l[s] = ra(ta(e, i, s));
		const u = e => we(l[e], 1, 1, (() => {
			l[e] = null
		}));
		let c = e[3] && na(e),
			d = e[4] && ia(e);
		return {
			c() {
				n && n.c(),
				s = T(),
				a = S("div");
				for (let e = 0; e < l.length; e += 1)
					l[e].c();
				t = T(),
				c && c.c(),
				o = T(),
				d && d.c(),
				A(a, "class", "board svelte-gexn6m")
			},
			m(e, i) {
				n && n.m(e, i),
				j(e, s, i),
				j(e, a, i);
				for (let e = 0; e < l.length; e += 1)
					l[e].m(a, null);
				$(a, t),
				c && c.m(a, null),
				$(a, o),
				d && d.m(a, null),
				r = !0
			},
			p(e, [r]) {
				if (e[6] ? n ? (n.p(e, r), 64 & r && ke(n, 1)) : (n = oa(e), n.c(), ke(n, 1), n.m(s.parentNode, s)) : n && (fe(), we(n, 1, 1, (() => {
					n = null
				})), be()), 8231 & r) {
					let s;
					for (i = e[0], s = 0; s < i.length; s += 1) {
						const o = ta(e, i, s);
						l[s] ? (l[s].p(o, r), ke(l[s], 1)) : (l[s] = ra(o), l[s].c(), ke(l[s], 1), l[s].m(a, t))
					}
					for (fe(), s = i.length; s < l.length; s += 1)
						u(s);
					be()
				}
				e[3] ? c ? c.p(e, r) : (c = na(e), c.c(), c.m(a, o)) : c && (c.d(1), c = null),
				e[4] ? d ? (d.p(e, r), 16 & r && ke(d, 1)) : (d = ia(e), d.c(), ke(d, 1), d.m(a, null)) : d && (fe(), we(d, 1, 1, (() => {
					d = null
				})), be())
			},
			i(e) {
				if (!r) {
					ke(n);
					for (let e = 0; e < i.length; e += 1)
						ke(l[e]);
					ke(d),
					r = !0
				}
			},
			o(e) {
				we(n),
				l = l.filter(Boolean);
				for (let e = 0; e < l.length; e += 1)
					we(l[e]);
				we(d),
				r = !1
			},
			d(e) {
				n && n.d(e),
				e && q(s),
				e && q(a),
				C(l, e),
				c && c.d(),
				d && d.d()
			}
		}
	}
	function ua(e, s, a) {
		let {value: t} = s,
			{board: o} = s,
			{guesses: r} = s,
			{icon: n} = s,
			{tutorial: i} = s;
		const l = K();
		let u = [],
			c = !1,
			d = 0,
			p = 0,
			m = 0,
			h = 0,
			y = "";
		function g(e, s, t, n) {
			if (r >= t) {
				a(9, m = e),
				a(10, h = s),
				a(6, c = !0),
				a(11, y = r > t ? n : "");
				const i = function(e, s) {
					const a = new Qe;
					for (let t = 0; t < e; ++t) {
						const e = new Set;
						for (let o = 0; o < 5; ++o) {
							const r = s.state[t][o],
								n = s.words[t][o];
							"⬛" !== r ? (e.has(n) ? a.incrementCount(n) : a.countConfirmed(n) || (e.add(n), a.setCount(n, 1)), "🟩" === r ? a.word[o].value = n : a.word[o].not(n)) : (a.confirmCount(n), a.inGlobalNotList(n) || a.word[o].not(n))
						}
					}
					let t = "";
					for (let e = 0; e < a.word.length; ++e)
						t += a.word[e].value ? a.word[e].value : `[^${[...a.lettersNotAt(e)].join(" ")}]`;
					return e => {
						if (new RegExp(t).test(e)) {
							const s = e.split("");
							for (const e of a.letterCounts) {
								const a = es(s, e[0]);
								if (!a || e[1][1] && a !== e[1][0])
									return !1
							}
							return !0
						}
						return !1
					}
				}(t, o);
				a(7, d = Ke.words.filter((e => i(e))).length),
				a(8, p = d + Ke.valid.filter((e => i(e))).length)
			}
		}
		return e.$$set = e => {
			"value" in e && a(0, t = e.value),
			"board" in e && a(1, o = e.board),
			"guesses" in e && a(2, r = e.guesses),
			"icon" in e && a(3, n = e.icon),
			"tutorial" in e && a(4, i = e.tutorial)
		}, [t, o, r, n, i, u, c, d, p, m, h, y, l, g, function(e) {
			u[e].shake()
		}, function(e) {
			u[e].bounce()
		}, function(e) {
			e && e.defaultPrevented || a(6, c = !1)
		}, function(e, s) {
			se[e ? "unshift" : "push"]((() => {
				u[s] = e,
				a(5, u)
			}))
		}, function(s, o) {
			e.$$.not_equal(t[o], s) && (t[o] = s, a(0, t))
		}, (e, s) => g(s.detail.x, s.detail.y, e, t[e]), () => l("closeTutPopUp")]
	}
	class ca extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, ua, la, n, {
				value: 0,
				board: 1,
				guesses: 2,
				icon: 3,
				tutorial: 4,
				shake: 14,
				bounce: 15,
				hideCtx: 16
			})
		}
		get shake()
		{
			return this.$$.ctx[14]
		}
		get bounce()
		{
			return this.$$.ctx[15]
		}
		get hideCtx()
		{
			return this.$$.ctx[16]
		}
	}
	function da(e) {
		let s,
			a,
			t,
			o,
			r,
			n;
		const i = e[4].default,
			u = l(i, e, e[3], null);
		return {
			c() {
				s = S("div"),
				a = M(e[0]),
				u && u.c(),
				A(s, "class", t = h(e[1]) + " svelte-1ymomqm"),
				R(s, "big", 1 !== e[0].length)
			},
			m(t, i) {
				j(t, s, i),
				$(s, a),
				u && u.m(s, null),
				o = !0,
				r || (n = E(s, "click", e[5]), r = !0)
			},
			p(e, [r]) {
				(!o || 1 & r) && H(a, e[0]),
				u && u.p && (!o || 8 & r) && d(u, i, e, e[3], o ? c(i, e[3], r, null) : p(e[3]), null),
				(!o || 2 & r && t !== (t = h(e[1]) + " svelte-1ymomqm")) && A(s, "class", t),
				3 & r && R(s, "big", 1 !== e[0].length)
			},
			i(e) {
				o || (ke(u, e), o = !0)
			},
			o(e) {
				we(u, e),
				o = !1
			},
			d(e) {
				e && q(s),
				u && u.d(e),
				r = !1,
				n()
			}
		}
	}
	function pa(e, s, a) {
		let {$$slots: t={}, $$scope: o} = s,
			{letter: r} = s,
			{state: n="🔳"} = s;
		const i = K();
		return e.$$set = e => {
			"letter" in e && a(0, r = e.letter),
			"state" in e && a(1, n = e.state),
			"$$scope" in e && a(3, o = e.$$scope)
		}, [r, n, i, o, t, () => i("keystroke", r)]
	}
	class ma extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, pa, da, n, {
				letter: 0,
				state: 1
			})
		}
	}
	function ha(e, s, a) {
		const t = e.slice();
		return t[13] = s[a], t
	}
	function ya(e, s, a) {
		const t = e.slice();
		return t[13] = s[a], t
	}
	function ga(e, s, a) {
		const t = e.slice();
		return t[13] = s[a], t
	}
	function fa(e) {
		let s,
			a;
		return s = new ma({
			props: {
				letter: e[13],
				state: e[2][e[13]]
			}
		}), s.$on("keystroke", e[8]), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				4 & a && (t.state = e[2][e[13]]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function ba(e) {
		let s,
			a;
		return s = new ma({
			props: {
				letter: e[13],
				state: e[2][e[13]]
			}
		}), s.$on("keystroke", e[9]), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				4 & a && (t.state = e[2][e[13]]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function ka(e) {
		let s,
			a;
		return s = new ma({
			props: {
				letter: e[13],
				state: e[2][e[13]]
			}
		}), s.$on("keystroke", e[11]), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				4 & a && (t.state = e[2][e[13]]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function wa(s) {
		let a,
			t;
		return {
			c() {
				a = N("svg"),
				t = N("path"),
				A(t, "d", "M22 3H7c-.69 0-1.23.35-1.59.88L0 12l5.41 8.11c.36.53.9.89 1.59.89h15c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H7.07L2.4 12l4.66-7H22v14zm-11.59-2L14 13.41 17.59 17 19 15.59 15.41 12 19 8.41 17.59 7 14 10.59 10.41 7 9 8.41 12.59 12 9 15.59z"),
				A(a, "xmlns", "http://www.w3.org/2000/svg"),
				A(a, "viewBox", "0 0 24 24"),
				A(a, "class", "svelte-bldt10")
			},
			m(e, s) {
				j(e, a, s),
				$(a, t)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function va(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m,
			h,
			y = ss[0],
			g = [];
		for (let s = 0; s < y.length; s += 1)
			g[s] = fa(ga(e, y, s));
		const f = e => we(g[e], 1, 1, (() => {
			g[e] = null
		}));
		let b = ss[1],
			k = [];
		for (let s = 0; s < b.length; s += 1)
			k[s] = ba(ya(e, b, s));
		const w = e => we(k[e], 1, 1, (() => {
			k[e] = null
		}));
		l = new ma({
			props: {
				letter: "ENTER"
			}
		}),
		l.$on("keystroke", e[10]);
		let v = ss[2],
			x = [];
		for (let s = 0; s < v.length; s += 1)
			x[s] = ka(ha(e, v, s));
		const z = e => we(x[e], 1, 1, (() => {
			x[e] = null
		}));
		return d = new ma({
			props: {
				letter: "",
				$$slots: {
					default: [wa]
				},
				$$scope: {
					ctx: e
				}
			}
		}), d.$on("keystroke", e[5]), {
			c() {
				s = T(),
				a = S("div"),
				t = S("div");
				for (let e = 0; e < g.length; e += 1)
					g[e].c();
				o = T(),
				r = S("div");
				for (let e = 0; e < k.length; e += 1)
					k[e].c();
				n = T(),
				i = S("div"),
				qe(l.$$.fragment),
				u = T();
				for (let e = 0; e < x.length; e += 1)
					x[e].c();
				c = T(),
				qe(d.$$.fragment),
				A(t, "class", "row svelte-bldt10"),
				A(r, "class", "row svelte-bldt10"),
				A(i, "class", "row svelte-bldt10"),
				A(a, "class", "keyboard svelte-bldt10"),
				R(a, "preventChange", e[1])
			},
			m(y, f) {
				j(y, s, f),
				j(y, a, f),
				$(a, t);
				for (let e = 0; e < g.length; e += 1)
					g[e].m(t, null);
				$(a, o),
				$(a, r);
				for (let e = 0; e < k.length; e += 1)
					k[e].m(r, null);
				$(a, n),
				$(a, i),
				Ce(l, i, null),
				$(i, u);
				for (let e = 0; e < x.length; e += 1)
					x[e].m(i, null);
				$(i, c),
				Ce(d, i, null),
				p = !0,
				m || (h = E(document.body, "keydown", e[6]), m = !0)
			},
			p(e, [s]) {
				if (20 & s) {
					let a;
					for (y = ss[0], a = 0; a < y.length; a += 1) {
						const o = ga(e, y, a);
						g[a] ? (g[a].p(o, s), ke(g[a], 1)) : (g[a] = fa(o), g[a].c(), ke(g[a], 1), g[a].m(t, null))
					}
					for (fe(), a = y.length; a < g.length; a += 1)
						f(a);
					be()
				}
				if (20 & s) {
					let a;
					for (b = ss[1], a = 0; a < b.length; a += 1) {
						const t = ya(e, b, a);
						k[a] ? (k[a].p(t, s), ke(k[a], 1)) : (k[a] = ba(t), k[a].c(), ke(k[a], 1), k[a].m(r, null))
					}
					for (fe(), a = b.length; a < k.length; a += 1)
						w(a);
					be()
				}
				if (20 & s) {
					let a;
					for (v = ss[2], a = 0; a < v.length; a += 1) {
						const t = ha(e, v, a);
						x[a] ? (x[a].p(t, s), ke(x[a], 1)) : (x[a] = ka(t), x[a].c(), ke(x[a], 1), x[a].m(i, c))
					}
					for (fe(), a = v.length; a < x.length; a += 1)
						z(a);
					be()
				}
				const o = {};
				1048576 & s && (o.$$scope = {
					dirty: s,
					ctx: e
				}),
				d.$set(o),
				2 & s && R(a, "preventChange", e[1])
			},
			i(e) {
				if (!p) {
					for (let e = 0; e < y.length; e += 1)
						ke(g[e]);
					for (let e = 0; e < b.length; e += 1)
						ke(k[e]);
					ke(l.$$.fragment, e);
					for (let e = 0; e < v.length; e += 1)
						ke(x[e]);
					ke(d.$$.fragment, e),
					p = !0
				}
			},
			o(e) {
				g = g.filter(Boolean);
				for (let e = 0; e < g.length; e += 1)
					we(g[e]);
				k = k.filter(Boolean);
				for (let e = 0; e < k.length; e += 1)
					we(k[e]);
				we(l.$$.fragment, e),
				x = x.filter(Boolean);
				for (let e = 0; e < x.length; e += 1)
					we(x[e]);
				we(d.$$.fragment, e),
				p = !1
			},
			d(e) {
				e && q(s),
				e && q(a),
				C(g, e),
				C(k, e),
				Se(l),
				C(x, e),
				Se(d),
				m = !1,
				h()
			}
		}
	}
	function $a(e, s, a) {
		let t;
		i(e, bs, (e => a(2, t = e)));
		let {value: o=""} = s,
			{disabled: r=!1} = s,
			n = !0;
		const l = K();
		function u(e) {
			!r && o.length < 5 && (l("keystroke", e), a(7, o += e))
		}
		function c() {
			r || a(7, o = o.slice(0, o.length - 1))
		}
		F(fs.subscribe((() => {
			a(1, n = !0),
			setTimeout((() => a(1, n = !1)), 200)
		})));
		return e.$$set = e => {
			"value" in e && a(7, o = e.value),
			"disabled" in e && a(0, r = e.disabled)
		}, [r, n, t, l, u, c, function(e) {
			if (!r && !e.ctrlKey && !e.altKey) {
				if (e.key && /^[a-z]$/.test(e.key.toLowerCase()))
					return u(e.key.toLowerCase());
				if ("Backspace" === e.key)
					return c();
				if ("Enter" === e.key)
					return l("submitWord")
			}
			"Escape" === e.key && l("esc")
		}, o, e => u(e.detail), e => u(e.detail), () => !r && l("submitWord"), e => u(e.detail)]
	}
	class xa extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, $a, va, n, {
				value: 7,
				disabled: 0
			})
		}
	}
	const za = e => ({}),
		ja = e => ({});
	function qa(e) {
		let s,
			a,
			t,
			r,
			n,
			i,
			u,
			m;
		r = new $s({
			props: {
				$$slots: {
					default: [Sa]
				},
				$$scope: {
					ctx: e
				}
			}
		});
		const h = e[3].default,
			y = l(h, e, e[4], null);
		return {
			c() {
				s = S("div"),
				a = S("div"),
				t = S("div"),
				qe(r.$$.fragment),
				n = T(),
				y && y.c(),
				A(t, "class", "exit svelte-ahphol"),
				A(a, "class", "modal svelte-ahphol"),
				A(s, "class", "overlay svelte-ahphol"),
				R(s, "visible", e[0])
			},
			m(o, l) {
				j(o, s, l),
				$(s, a),
				$(a, t),
				Ce(r, t, null),
				$(a, n),
				y && y.m(a, null),
				i = !0,
				u || (m = [E(t, "click", e[2]), E(s, "click", L(e[2]))], u = !0)
			},
			p(e, a) {
				const t = {};
				16 & a && (t.$$scope = {
					dirty: a,
					ctx: e
				}),
				r.$set(t),
				y && y.p && (!i || 16 & a) && d(y, h, e, e[4], i ? c(h, e[4], a, null) : p(e[4]), null),
				1 & a && R(s, "visible", e[0])
			},
			i(e) {
				i || (ke(r.$$.fragment, e), ke(y, e), i = !0)
			},
			o(e) {
				we(r.$$.fragment, e),
				we(y, e),
				i = !1
			},
			d(e) {
				e && q(s),
				Se(r),
				y && y.d(e),
				u = !1,
				o(m)
			}
		}
	}
	function Ca(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			u,
			m;
		t = new $s({
			props: {
				$$slots: {
					default: [Na]
				},
				$$scope: {
					ctx: e
				}
			}
		});
		const h = e[3].default,
			y = l(h, e, e[4], null),
			g = e[3].footer,
			f = l(g, e, e[4], ja);
		return {
			c() {
				s = S("div"),
				a = S("div"),
				qe(t.$$.fragment),
				o = T(),
				r = S("div"),
				y && y.c(),
				n = T(),
				f && f.c(),
				A(a, "class", "exit svelte-ahphol"),
				A(s, "class", "page svelte-ahphol"),
				R(s, "visible", e[0])
			},
			m(l, c) {
				j(l, s, c),
				$(s, a),
				Ce(t, a, null),
				$(s, o),
				$(s, r),
				y && y.m(r, null),
				$(s, n),
				f && f.m(s, null),
				i = !0,
				u || (m = E(a, "click", e[2]), u = !0)
			},
			p(e, a) {
				const o = {};
				16 & a && (o.$$scope = {
					dirty: a,
					ctx: e
				}),
				t.$set(o),
				y && y.p && (!i || 16 & a) && d(y, h, e, e[4], i ? c(h, e[4], a, null) : p(e[4]), null),
				f && f.p && (!i || 16 & a) && d(f, g, e, e[4], i ? c(g, e[4], a, za) : p(e[4]), ja),
				1 & a && R(s, "visible", e[0])
			},
			i(e) {
				i || (ke(t.$$.fragment, e), ke(y, e), ke(f, e), i = !0)
			},
			o(e) {
				we(t.$$.fragment, e),
				we(y, e),
				we(f, e),
				i = !1
			},
			d(e) {
				e && q(s),
				Se(t),
				y && y.d(e),
				f && f.d(e),
				u = !1,
				m()
			}
		}
	}
	function Sa(s) {
		let a;
		return {
			c() {
				a = N("path"),
				A(a, "d", "M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Na(s) {
		let a;
		return {
			c() {
				a = N("path"),
				A(a, "d", "M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Ma(e) {
		let s,
			a,
			t,
			o;
		const r = [Ca, qa],
			n = [];
		function i(e, s) {
			return e[1] ? 0 : 1
		}
		return s = i(e), a = n[s] = r[s](e), {
			c() {
				a.c(),
				t = O()
			},
			m(e, a) {
				n[s].m(e, a),
				j(e, t, a),
				o = !0
			},
			p(e, [o]) {
				let l = s;
				s = i(e),
				s === l ? n[s].p(e, o) : (fe(), we(n[l], 1, 1, (() => {
					n[l] = null
				})), be(), a = n[s], a ? a.p(e, o) : (a = n[s] = r[s](e), a.c()), ke(a, 1), a.m(t.parentNode, t))
			},
			i(e) {
				o || (ke(a), o = !0)
			},
			o(e) {
				we(a),
				o = !1
			},
			d(e) {
				n[s].d(e),
				e && q(t)
			}
		}
	}
	function Ta(e, s, a) {
		let {$$slots: t={}, $$scope: o} = s,
			{visible: r=!1} = s,
			{fullscreen: n=!1} = s;
		const i = K();
		return e.$$set = e => {
			"visible" in e && a(0, r = e.visible),
			"fullscreen" in e && a(1, n = e.fullscreen),
			"$$scope" in e && a(4, o = e.$$scope)
		}, [r, n, function() {
			a(0, r = !1),
			i("close")
		}, t, o]
	}
	class Oa extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Ta, Ma, n, {
				visible: 0,
				fullscreen: 1
			})
		}
	}
	function Ea(s) {
		let a,
			t,
			o;
		return {
			c() {
				a = S("div"),
				A(a, "disabled", s[1]),
				A(a, "class", "svelte-16o9p8g"),
				R(a, "checked", s[0])
			},
			m(e, r) {
				j(e, a, r),
				t || (o = E(a, "click", s[2]), t = !0)
			},
			p(e, [s]) {
				2 & s && A(a, "disabled", e[1]),
				1 & s && R(a, "checked", e[0])
			},
			i: e,
			o: e,
			d(e) {
				e && q(a),
				t = !1,
				o()
			}
		}
	}
	function _a(e, s, a) {
		let {value: t} = s,
			{disabled: o=!1} = s;
		return e.$$set = e => {
			"value" in e && a(0, t = e.value),
			"disabled" in e && a(1, o = e.disabled)
		}, [t, o, e => !o && a(0, t = !t)]
	}
	class La extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, _a, Ea, n, {
				value: 0,
				disabled: 1
			})
		}
	}
	function Aa(e, s, a) {
		const t = e.slice();
		return t[4] = s[a], t[6] = a, t
	}
	function Ha(e) {
		let s,
			a,
			t,
			o = e[4] + "";
		return {
			c() {
				s = S("option"),
				a = M(o),
				s.__value = t = e[6],
				s.value = s.__value
			},
			m(e, t) {
				j(e, s, t),
				$(s, a)
			},
			p(e, s) {
				2 & s && o !== (o = e[4] + "") && H(a, o)
			},
			d(e) {
				e && q(s)
			}
		}
	}
	function Ia(s) {
		let a,
			t,
			o,
			r = s[1],
			n = [];
		for (let e = 0; e < r.length; e += 1)
			n[e] = Ha(Aa(s, r, e));
		return {
			c() {
				a = S("select");
				for (let e = 0; e < n.length; e += 1)
					n[e].c();
				a.disabled = s[2],
				A(a, "class", "svelte-2btkgx"),
				void 0 === s[0] && ne((() => s[3].call(a)))
			},
			m(e, r) {
				j(e, a, r);
				for (let e = 0; e < n.length; e += 1)
					n[e].m(a, null);
				D(a, s[0]),
				t || (o = E(a, "change", s[3]), t = !0)
			},
			p(e, [s]) {
				if (2 & s) {
					let t;
					for (r = e[1], t = 0; t < r.length; t += 1) {
						const o = Aa(e, r, t);
						n[t] ? n[t].p(o, s) : (n[t] = Ha(o), n[t].c(), n[t].m(a, null))
					}
					for (; t < n.length; t += 1)
						n[t].d(1);
					n.length = r.length
				}
				4 & s && (a.disabled = e[2]),
				1 & s && D(a, e[0])
			},
			i: e,
			o: e,
			d(e) {
				e && q(a),
				C(n, e),
				t = !1,
				o()
			}
		}
	}
	function Da(e, s, a) {
		let {value: t} = s,
			{options: o} = s,
			{disabled: r=!1} = s;
		return e.$$set = e => {
			"value" in e && a(0, t = e.value),
			"options" in e && a(1, o = e.options),
			"disabled" in e && a(2, r = e.disabled)
		}, [t, o, r, function() {
			t = function(e) {
				const s = e.querySelector(":checked") || e.options[0];
				return s && s.__value
			}(this),
			a(0, t)
		}]
	}
	class Ra extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Da, Ia, n, {
				value: 0,
				options: 1,
				disabled: 2
			})
		}
	}
	const Ua = e => ({}),
		Ba = e => ({}),
		Wa = e => ({}),
		Ga = e => ({});
	function Pa(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			u,
			m;
		const h = e[6].title,
			y = l(h, e, e[5], Ga),
			g = e[6].desc,
			f = l(g, e, e[5], Ba);
		function b(s) {
			e[7](s)
		}
		var k = e[4][e[1]];
		function w(e) {
			let s = {
				options: e[2],
				disabled: e[3]
			};
			return void 0 !== e[0] && (s.value = e[0]), {
				props: s
			}
		}
		return k && (i = new k(w(e)), se.push((() => je(i, "value", b)))), {
			c() {
				s = S("div"),
				a = S("div"),
				t = S("div"),
				y && y.c(),
				o = T(),
				r = S("div"),
				f && f.c(),
				n = T(),
				i && qe(i.$$.fragment),
				A(t, "class", "title svelte-40b4uj"),
				A(r, "class", "desc svelte-40b4uj"),
				A(s, "class", "setting svelte-40b4uj")
			},
			m(e, l) {
				j(e, s, l),
				$(s, a),
				$(a, t),
				y && y.m(t, null),
				$(a, o),
				$(a, r),
				f && f.m(r, null),
				$(s, n),
				i && Ce(i, s, null),
				m = !0
			},
			p(e, [a]) {
				y && y.p && (!m || 32 & a) && d(y, h, e, e[5], m ? c(h, e[5], a, Wa) : p(e[5]), Ga),
				f && f.p && (!m || 32 & a) && d(f, g, e, e[5], m ? c(g, e[5], a, Ua) : p(e[5]), Ba);
				const t = {};
				if (4 & a && (t.options = e[2]), 8 & a && (t.disabled = e[3]), !u && 1 & a && (u = !0, t.value = e[0], ie((() => u = !1))), k !== (k = e[4][e[1]])) {
					if (i) {
						fe();
						const e = i;
						we(e.$$.fragment, 1, 0, (() => {
							Se(e, 1)
						})),
						be()
					}
					k ? (i = new k(w(e)), se.push((() => je(i, "value", b))), qe(i.$$.fragment), ke(i.$$.fragment, 1), Ce(i, s, null)) : i = null
				} else
					k && i.$set(t)
			},
			i(e) {
				m || (ke(y, e), ke(f, e), i && ke(i.$$.fragment, e), m = !0)
			},
			o(e) {
				we(y, e),
				we(f, e),
				i && we(i.$$.fragment, e),
				m = !1
			},
			d(e) {
				e && q(s),
				y && y.d(e),
				f && f.d(e),
				i && Se(i)
			}
		}
	}
	function Ja(e, s, a) {
		let {$$slots: t={}, $$scope: o} = s,
			{value: r} = s,
			{type: n} = s,
			{options: i=[]} = s,
			{disabled: l=!1} = s;
		const u = {
			switch: La,
			dropdown: Ra
		};
		return e.$$set = e => {
			"value" in e && a(0, r = e.value),
			"type" in e && a(1, n = e.type),
			"options" in e && a(2, i = e.options),
			"disabled" in e && a(3, l = e.disabled),
			"$$scope" in e && a(5, o = e.$$scope)
		}, [r, n, i, l, u, o, t, function(e) {
			r = e,
			a(0, r)
		}]
	}
	class Ya extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Ja, Pa, n, {
				value: 0,
				type: 1,
				options: 2,
				disabled: 3
			})
		}
	}
	function Va(s) {
		let a;
		return {
			c() {
				a = S("span"),
				a.textContent = "Hard Mode",
				A(a, "slot", "title")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Xa(s) {
		let a;
		return {
			c() {
				a = S("span"),
				a.textContent = "Any revealed hints must be used in subsequent guesses",
				A(a, "slot", "desc")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Fa(s) {
		let a;
		return {
			c() {
				a = S("span"),
				a.textContent = "Dark Theme",
				A(a, "slot", "title")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Ka(s) {
		let a;
		return {
			c() {
				a = S("span"),
				a.textContent = "Color Blind Mode",
				A(a, "slot", "title")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Za(s) {
		let a;
		return {
			c() {
				a = S("span"),
				a.textContent = "High contrast colors",
				A(a, "slot", "desc")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Qa(s) {
		let a;
		return {
			c() {
				a = S("span"),
				a.textContent = "Game Mode",
				A(a, "slot", "title")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function et(s) {
		let a;
		return {
			c() {
				a = S("span"),
				a.textContent = "The game mode determines how often the word refreshes",
				A(a, "slot", "desc")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function st(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m,
			h,
			y,
			g,
			f,
			b,
			k,
			w,
			v;
		function x(s) {
			e[5](s)
		}
		let z = {
			type: "switch",
			disabled: !e[0].validHard,
			$$slots: {
				desc: [Xa],
				title: [Va]
			},
			$$scope: {
				ctx: e
			}
		};
		function C(s) {
			e[7](s)
		}
		void 0 !== e[1].hard[e[2]] && (z.value = e[1].hard[e[2]]),
		n = new Ya({
			props: z
		}),
		se.push((() => je(n, "value", x)));
		let N = {
			type: "switch",
			$$slots: {
				title: [Fa]
			},
			$$scope: {
				ctx: e
			}
		};
		function M(s) {
			e[8](s)
		}
		void 0 !== e[1].dark && (N.value = e[1].dark),
		u = new Ya({
			props: N
		}),
		se.push((() => je(u, "value", C)));
		let O = {
			type: "switch",
			$$slots: {
				desc: [Za],
				title: [Ka]
			},
			$$scope: {
				ctx: e
			}
		};
		function _(s) {
			e[9](s)
		}
		void 0 !== e[1].colorblind && (O.value = e[1].colorblind),
		p = new Ya({
			props: O
		}),
		se.push((() => je(p, "value", M)));
		let L = {
			type: "dropdown",
			options: ts.modes.map(at),
			$$slots: {
				desc: [et],
				title: [Qa]
			},
			$$scope: {
				ctx: e
			}
		};
		return void 0 !== e[2] && (L.value = e[2]), y = new Ya({
			props: L
		}), se.push((() => je(y, "value", _))), {
			c() {
				s = S("div"),
				a = S("div"),
				t = S("h3"),
				t.textContent = "settings",
				o = T(),
				r = S("div"),
				qe(n.$$.fragment),
				l = T(),
				qe(u.$$.fragment),
				d = T(),
				qe(p.$$.fragment),
				h = T(),
				qe(y.$$.fragment),
				f = T(),
				b = S("div"),
				b.innerHTML = '<a href="https://github.com/MikhaD/wordle" target="_blank">Leave a ⭐</a> \n\t\t\t<a href="https://github.com/MikhaD/wordle/issues" target="_blank">Report a Bug</a>',
				A(b, "class", "links svelte-1mwrm1x"),
				A(a, "class", "settings-top"),
				A(s, "class", "outer svelte-1mwrm1x")
			},
			m(i, c) {
				j(i, s, c),
				$(s, a),
				$(a, t),
				$(a, o),
				$(a, r),
				Ce(n, r, null),
				$(a, l),
				Ce(u, a, null),
				$(a, d),
				Ce(p, a, null),
				$(a, h),
				Ce(y, a, null),
				$(a, f),
				$(a, b),
				k = !0,
				w || (v = E(r, "click", e[6]), w = !0)
			},
			p(e, [s]) {
				const a = {};
				1 & s && (a.disabled = !e[0].validHard),
				1024 & s && (a.$$scope = {
					dirty: s,
					ctx: e
				}),
				!i && 6 & s && (i = !0, a.value = e[1].hard[e[2]], ie((() => i = !1))),
				n.$set(a);
				const t = {};
				1024 & s && (t.$$scope = {
					dirty: s,
					ctx: e
				}),
				!c && 2 & s && (c = !0, t.value = e[1].dark, ie((() => c = !1))),
				u.$set(t);
				const o = {};
				1024 & s && (o.$$scope = {
					dirty: s,
					ctx: e
				}),
				!m && 2 & s && (m = !0, o.value = e[1].colorblind, ie((() => m = !1))),
				p.$set(o);
				const r = {};
				1024 & s && (r.$$scope = {
					dirty: s,
					ctx: e
				}),
				!g && 4 & s && (g = !0, r.value = e[2], ie((() => g = !1))),
				y.$set(r)
			},
			i(e) {
				k || (ke(n.$$.fragment, e), ke(u.$$.fragment, e), ke(p.$$.fragment, e), ke(y.$$.fragment, e), k = !0)
			},
			o(e) {
				we(n.$$.fragment, e),
				we(u.$$.fragment, e),
				we(p.$$.fragment, e),
				we(y.$$.fragment, e),
				k = !1
			},
			d(e) {
				e && q(s),
				Se(n),
				Se(u),
				Se(p),
				Se(y),
				w = !1,
				v()
			}
		}
	}
	const at = e => e.name;
	function tt(e, s, a) {
		let t,
			o;
		i(e, ks, (e => a(1, t = e))),
		i(e, fs, (e => a(2, o = e)));
		let {state: r} = s;
		const n = Q("toaster");
		let l;
		X((() => {
			a(4, l = document.documentElement)
		}));
		return e.$$set = e => {
			"state" in e && a(0, r = e.state)
		}, e.$$.update = () => {
			18 & e.$$.dirty && l && (t.dark ? l.classList.remove("light") : l.classList.add("light"), t.colorblind ? l.classList.add("colorblind") : l.classList.remove("colorblind"), localStorage.setItem("settings", JSON.stringify(t)))
		}, [r, t, o, n, l, function(s) {
			e.$$.not_equal(t.hard[o], s) && (t.hard[o] = s, ks.set(t))
		}, () => {
			r.validHard || n.pop("Game has already violated hard mode")
		}, function(s) {
			e.$$.not_equal(t.dark, s) && (t.dark = s, ks.set(t))
		}, function(s) {
			e.$$.not_equal(t.colorblind, s) && (t.colorblind = s, ks.set(t))
		}, function(e) {
			o = e,
			fs.set(o)
		}]
	}
	class ot extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, tt, st, n, {
				state: 0
			})
		}
	}
	const rt = e => ({}),
		nt = e => ({}),
		it = e => ({}),
		lt = e => ({});
	function ut(e) {
		let s,
			a,
			t,
			o,
			r;
		const n = e[2][1],
			i = l(n, e, e[1], lt),
			u = e[2][2],
			m = l(u, e, e[1], nt);
		return {
			c() {
				s = S("div"),
				a = S("div"),
				i && i.c(),
				t = T(),
				o = S("div"),
				m && m.c(),
				A(a, "class", "svelte-1cu43ge"),
				A(o, "class", "svelte-1cu43ge"),
				A(s, "class", "sep svelte-1cu43ge"),
				R(s, "visible", e[0])
			},
			m(e, n) {
				j(e, s, n),
				$(s, a),
				i && i.m(a, null),
				$(s, t),
				$(s, o),
				m && m.m(o, null),
				r = !0
			},
			p(e, [a]) {
				i && i.p && (!r || 2 & a) && d(i, n, e, e[1], r ? c(n, e[1], a, it) : p(e[1]), lt),
				m && m.p && (!r || 2 & a) && d(m, u, e, e[1], r ? c(u, e[1], a, rt) : p(e[1]), nt),
				1 & a && R(s, "visible", e[0])
			},
			i(e) {
				r || (ke(i, e), ke(m, e), r = !0)
			},
			o(e) {
				we(i, e),
				we(m, e),
				r = !1
			},
			d(e) {
				e && q(s),
				i && i.d(e),
				m && m.d(e)
			}
		}
	}
	function ct(e, s, a) {
		let {$$slots: t={}, $$scope: o} = s,
			{visible: r=!0} = s;
		return e.$$set = e => {
			"visible" in e && a(0, r = e.visible),
			"$$scope" in e && a(1, o = e.$$scope)
		}, [r, o, t]
	}
	class dt extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, ct, ut, n, {
				visible: 0
			})
		}
	}
	function pt(s) {
		let a,
			t,
			o;
		return {
			c() {
				a = S("div"),
				a.innerHTML = 'share\n\t<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path fill="white" d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92zM18 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 7.02c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"></path></svg>',
				A(a, "class", "svelte-gyq5p4")
			},
			m(e, r) {
				j(e, a, r),
				t || (o = E(a, "click", s[4]), t = !0)
			},
			p: e,
			i: e,
			o: e,
			d(e) {
				e && q(a),
				t = !1,
				o()
			}
		}
	}
	function mt(e, s, a) {
		let t,
			o;
		i(e, fs, (e => a(3, o = e)));
		let {state: r} = s;
		const n = Q("toaster");
		return e.$$set = e => {
			"state" in e && a(2, r = e.state)
		}, e.$$.update = () => {
			12 & e.$$.dirty && a(0, t = `${ts.modes[o].name} Wordle+ #${r.wordNumber} ${ds(r) ? "X" : r.guesses}/${r.board.words.length}\n\n    ${r.board.state.slice(0, r.guesses).map((e => e.join(""))).join("\n    ")}\nmikhad.github.io/wordle`)
		}, [t, n, r, o, () => {
			navigator.clipboard.writeText(t),
			n.pop("Copied")
		}]
	}
	class ht extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, mt, pt, n, {
				state: 2
			})
		}
	}
	function yt(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m,
			h,
			y,
			g,
			f,
			b,
			k,
			w,
			v,
			x,
			z,
			C,
			N,
			O,
			E,
			_,
			L,
			H,
			I,
			D,
			U,
			B,
			W,
			G,
			P,
			J,
			Y,
			V,
			X,
			F,
			K,
			Z,
			Q,
			ee,
			se,
			ae,
			te,
			oe,
			re,
			ne,
			ie,
			le,
			ue,
			ce,
			de;
		return b = new Ls({
			props: {
				value: "w",
				state: "🟩"
			}
		}), w = new Ls({
			props: {
				value: "e",
				state: "🔳"
			}
		}), x = new Ls({
			props: {
				value: "a",
				state: "🔳"
			}
		}), C = new Ls({
			props: {
				value: "r",
				state: "🔳"
			}
		}), O = new Ls({
			props: {
				value: "y",
				state: "🔳"
			}
		}), I = new Ls({
			props: {
				value: "p",
				state: "🔳"
			}
		}), U = new Ls({
			props: {
				value: "i",
				state: "🟨"
			}
		}), W = new Ls({
			props: {
				value: "l",
				state: "🔳"
			}
		}), P = new Ls({
			props: {
				value: "l",
				state: "🔳"
			}
		}), Y = new Ls({
			props: {
				value: "s",
				state: "🔳"
			}
		}), Z = new Ls({
			props: {
				value: "v",
				state: "🔳"
			}
		}), ee = new Ls({
			props: {
				value: "a",
				state: "🔳"
			}
		}), ae = new Ls({
			props: {
				value: "g",
				state: "🔳"
			}
		}), oe = new Ls({
			props: {
				value: "u",
				state: "⬛"
			}
		}), ne = new Ls({
			props: {
				value: "e",
				state: "🔳"
			}
		}), {
			c() {
				s = S("h3"),
				s.textContent = "how to play",
				a = T(),
				t = S("div"),
				o = M("Guess the "),
				r = S("strong"),
				r.textContent = "WORDLE",
				n = M(" in "),
				i = M(6),
				l = M(" tries."),
				u = T(),
				c = S("div"),
				c.textContent = "Each guess must be a valid 5 letter word. Hit the enter button to submit.",
				d = T(),
				p = S("div"),
				p.textContent = "After each guess, the color of the tiles will change to show how close your guess was to the\n\tword.",
				m = T(),
				h = S("div"),
				y = S("div"),
				y.innerHTML = "<strong>Examples</strong>",
				g = T(),
				f = S("div"),
				qe(b.$$.fragment),
				k = T(),
				qe(w.$$.fragment),
				v = T(),
				qe(x.$$.fragment),
				z = T(),
				qe(C.$$.fragment),
				N = T(),
				qe(O.$$.fragment),
				E = T(),
				_ = S("div"),
				_.innerHTML = "The letter <strong>W</strong> is in the word and in the correct spot.",
				L = T(),
				H = S("div"),
				qe(I.$$.fragment),
				D = T(),
				qe(U.$$.fragment),
				B = T(),
				qe(W.$$.fragment),
				G = T(),
				qe(P.$$.fragment),
				J = T(),
				qe(Y.$$.fragment),
				V = T(),
				X = S("div"),
				X.innerHTML = "The letter <strong>I</strong> is in the word but in the wrong spot.",
				F = T(),
				K = S("div"),
				qe(Z.$$.fragment),
				Q = T(),
				qe(ee.$$.fragment),
				se = T(),
				qe(ae.$$.fragment),
				te = T(),
				qe(oe.$$.fragment),
				re = T(),
				qe(ne.$$.fragment),
				ie = T(),
				le = S("div"),
				le.innerHTML = "The letter <strong>U</strong> is not in the word in any spot.",
				ue = T(),
				ce = S("div"),
				ce.innerHTML = 'This is a recreation of the original <a href="https://www.nytimes.com/games/wordle/" target="_blank">Wordle</a>\n\tby Josh Wardle with additional modes and features, allowing you to play infinite wordles. Switch\n\tto infinite mode to play an unlimited number of times.\n\t<br/><br/>\n\tOpen the settings menu to see some of the additional features.\n\t<br/>\n\tWritten with Svelte, in Typescript by\n\t<a href="https://github.com/MikhaD" target="_blank">MikhaD</a>.',
				A(t, "class", "svelte-6daei"),
				A(c, "class", "svelte-6daei"),
				A(p, "class", "svelte-6daei"),
				A(y, "class", "svelte-6daei"),
				A(f, "class", "row svelte-6daei"),
				A(_, "class", "svelte-6daei"),
				A(H, "class", "row svelte-6daei"),
				A(X, "class", "svelte-6daei"),
				A(K, "class", "row svelte-6daei"),
				A(le, "class", "svelte-6daei"),
				A(h, "class", "examples svelte-6daei"),
				R(h, "complete", e[0]),
				A(ce, "class", "svelte-6daei")
			},
			m(e, q) {
				j(e, s, q),
				j(e, a, q),
				j(e, t, q),
				$(t, o),
				$(t, r),
				$(t, n),
				$(t, i),
				$(t, l),
				j(e, u, q),
				j(e, c, q),
				j(e, d, q),
				j(e, p, q),
				j(e, m, q),
				j(e, h, q),
				$(h, y),
				$(h, g),
				$(h, f),
				Ce(b, f, null),
				$(f, k),
				Ce(w, f, null),
				$(f, v),
				Ce(x, f, null),
				$(f, z),
				Ce(C, f, null),
				$(f, N),
				Ce(O, f, null),
				$(h, E),
				$(h, _),
				$(h, L),
				$(h, H),
				Ce(I, H, null),
				$(H, D),
				Ce(U, H, null),
				$(H, B),
				Ce(W, H, null),
				$(H, G),
				Ce(P, H, null),
				$(H, J),
				Ce(Y, H, null),
				$(h, V),
				$(h, X),
				$(h, F),
				$(h, K),
				Ce(Z, K, null),
				$(K, Q),
				Ce(ee, K, null),
				$(K, se),
				Ce(ae, K, null),
				$(K, te),
				Ce(oe, K, null),
				$(K, re),
				Ce(ne, K, null),
				$(h, ie),
				$(h, le),
				j(e, ue, q),
				j(e, ce, q),
				de = !0
			},
			p(e, [s]) {
				1 & s && R(h, "complete", e[0])
			},
			i(e) {
				de || (ke(b.$$.fragment, e), ke(w.$$.fragment, e), ke(x.$$.fragment, e), ke(C.$$.fragment, e), ke(O.$$.fragment, e), ke(I.$$.fragment, e), ke(U.$$.fragment, e), ke(W.$$.fragment, e), ke(P.$$.fragment, e), ke(Y.$$.fragment, e), ke(Z.$$.fragment, e), ke(ee.$$.fragment, e), ke(ae.$$.fragment, e), ke(oe.$$.fragment, e), ke(ne.$$.fragment, e), de = !0)
			},
			o(e) {
				we(b.$$.fragment, e),
				we(w.$$.fragment, e),
				we(x.$$.fragment, e),
				we(C.$$.fragment, e),
				we(O.$$.fragment, e),
				we(I.$$.fragment, e),
				we(U.$$.fragment, e),
				we(W.$$.fragment, e),
				we(P.$$.fragment, e),
				we(Y.$$.fragment, e),
				we(Z.$$.fragment, e),
				we(ee.$$.fragment, e),
				we(ae.$$.fragment, e),
				we(oe.$$.fragment, e),
				we(ne.$$.fragment, e),
				de = !1
			},
			d(e) {
				e && q(s),
				e && q(a),
				e && q(t),
				e && q(u),
				e && q(c),
				e && q(d),
				e && q(p),
				e && q(m),
				e && q(h),
				Se(b),
				Se(w),
				Se(x),
				Se(C),
				Se(O),
				Se(I),
				Se(U),
				Se(W),
				Se(P),
				Se(Y),
				Se(Z),
				Se(ee),
				Se(ae),
				Se(oe),
				Se(ne),
				e && q(ue),
				e && q(ce)
			}
		}
	}
	function gt(e, s, a) {
		let {visible: t} = s;
		return e.$$set = e => {
			"visible" in e && a(0, t = e.visible)
		}, [t]
	}
	class ft extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, gt, yt, n, {
				visible: 0
			})
		}
	}
	function bt(s) {
		let a,
			t,
			o;
		return {
			c() {
				a = S("div"),
				a.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svelte-1pg4aff"><path d="M4.609 12c0-4.082 3.309-7.391 7.391-7.391a7.39 7.39 0 0 1 6.523 3.912l-1.653 1.567H22v-5.13l-1.572 1.659C18.652 3.841 15.542 2 12 2 6.477 2 2 6.477 2 12s4.477 10 10 10c4.589 0 8.453-3.09 9.631-7.301l-2.512-.703c-.871 3.113-3.73 5.395-7.119 5.395-4.082 0-7.391-3.309-7.391-7.391z"></path></svg>',
				A(a, "class", "button svelte-1pg4aff")
			},
			m(e, r) {
				j(e, a, r),
				t || (o = E(a, "click", s[4]), t = !0)
			},
			p: e,
			d(e) {
				e && q(a),
				t = !1,
				o()
			}
		}
	}
	function kt(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i = `${Math.floor(e[0] / Ve.HOUR)}`.padStart(2, "0") + "",
			l = `${Math.floor(e[0] % Ve.HOUR / Ve.MINUTE)}`.padStart(2, "0") + "",
			u = `${Math.floor(e[0] % Ve.MINUTE / Ve.SECOND)}`.padStart(2, "0") + "";
		return {
			c() {
				s = S("div"),
				a = M(i),
				t = M(":"),
				o = M(l),
				r = M(":"),
				n = M(u),
				A(s, "class", "timer svelte-1pg4aff")
			},
			m(e, i) {
				j(e, s, i),
				$(s, a),
				$(s, t),
				$(s, o),
				$(s, r),
				$(s, n)
			},
			p(e, s) {
				1 & s && i !== (i = `${Math.floor(e[0] / Ve.HOUR)}`.padStart(2, "0") + "") && H(a, i),
				1 & s && l !== (l = `${Math.floor(e[0] % Ve.HOUR / Ve.MINUTE)}`.padStart(2, "0") + "") && H(o, l),
				1 & s && u !== (u = `${Math.floor(e[0] % Ve.MINUTE / Ve.SECOND)}`.padStart(2, "0") + "") && H(n, u)
			},
			d(e) {
				e && q(s)
			}
		}
	}
	function wt(s) {
		let a,
			t,
			o;
		function r(e, s) {
			return e[0] > 0 ? kt : bt
		}
		let n = r(s),
			i = n(s);
		return {
			c() {
				a = S("h3"),
				a.textContent = "Next wordle",
				t = T(),
				o = S("div"),
				i.c(),
				A(a, "class", "svelte-1pg4aff"),
				A(o, "class", "container svelte-1pg4aff")
			},
			m(e, s) {
				j(e, a, s),
				j(e, t, s),
				j(e, o, s),
				i.m(o, null)
			},
			p(e, [s]) {
				n === (n = r(e)) && i ? i.p(e, s) : (i.d(1), i = n(e), i && (i.c(), i.m(o, null)))
			},
			i: e,
			o: e,
			d(e) {
				e && q(a),
				e && q(t),
				e && q(o),
				i.d()
			}
		}
	}
	function vt(e, s, a) {
		let t;
		i(e, fs, (e => a(3, t = e)));
		const o = K();
		let r,
			n = 1e3;
		function l(e) {
			clearInterval(r),
			a(0, n = cs(ts.modes[e])),
			n < 0 && o("timeup"),
			r = setInterval((() => {
				a(0, n = cs(ts.modes[e])),
				n < 0 && (clearInterval(r), o("timeup"))
			}), Ve.SECOND)
		}
		return e.$$.update = () => {
			8 & e.$$.dirty && l(t)
		}, [n, o, l, t, () => o("reload")]
	}
	class $t extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, vt, wt, n, {
				reset: 2
			})
		}
		get reset()
		{
			return this.$$.ctx[2]
		}
	}
	function xt(e, s, a) {
		const t = e.slice();
		return t[2] = s[a], t
	}
	function zt(a) {
		let t,
			n,
			i,
			l,
			u = a[2] + "";
		return {
			c() {
				t = S("div"),
				n = M(u),
				A(t, "class", "slice svelte-1dgg1bc")
			},
			m(e, s) {
				j(e, t, s),
				$(t, n),
				l = !0
			},
			p(e, s) {
				(!l || 1 & s) && u !== (u = e[2] + "") && H(n, u)
			},
			i(e) {
				l || (i && i.end(1), l = !0)
			},
			o(a) {
				i = function(a, t, n) {
					let i,
						l = t(a, n),
						u = !0;
					const c = ge;
					function d() {
						const {delay: t=0, duration: r=300, easing: n=s, tick: d=e, css: p} = l || ve;
						p && (i = P(a, 1, 0, r, t, n, p));
						const m = f() + t,
							h = m + r;
						ne((() => he(a, !1, "start"))),
						v((e => {
							if (u) {
								if (e >= h)
									return d(0, 1), he(a, !1, "end"), --c.r || o(c.c), !1;
								if (e >= m) {
									const s = n((e - m) / r);
									d(1 - s, s)
								}
							}
							return u
						}))
					}
					return c.r += 1, r(l) ? me().then((() => {
						l = l(),
						d()
					})) : d(), {
						end(e) {
							e && l.tick && l.tick(1, 0),
							u && (i && J(a, i), u = !1)
						}
					}
				}(t, ms, {
					duration: 200
				}),
				l = !1
			},
			d(e) {
				e && q(t),
				e && i && i.end()
			}
		}
	}
	function jt(e) {
		let s,
			a,
			t = e[0],
			o = [];
		for (let s = 0; s < t.length; s += 1)
			o[s] = zt(xt(e, t, s));
		const r = e => we(o[e], 1, 1, (() => {
			o[e] = null
		}));
		return {
			c() {
				s = S("div");
				for (let e = 0; e < o.length; e += 1)
					o[e].c();
				A(s, "class", "toast svelte-1dgg1bc")
			},
			m(e, t) {
				j(e, s, t);
				for (let e = 0; e < o.length; e += 1)
					o[e].m(s, null);
				a = !0
			},
			p(e, [a]) {
				if (1 & a) {
					let n;
					for (t = e[0], n = 0; n < t.length; n += 1) {
						const r = xt(e, t, n);
						o[n] ? (o[n].p(r, a), ke(o[n], 1)) : (o[n] = zt(r), o[n].c(), ke(o[n], 1), o[n].m(s, null))
					}
					for (fe(), n = t.length; n < o.length; n += 1)
						r(n);
					be()
				}
			},
			i(e) {
				if (!a) {
					for (let e = 0; e < t.length; e += 1)
						ke(o[e]);
					a = !0
				}
			},
			o(e) {
				o = o.filter(Boolean);
				for (let e = 0; e < o.length; e += 1)
					we(o[e]);
				a = !1
			},
			d(e) {
				e && q(s),
				C(o, e)
			}
		}
	}
	function qt(e, s, a) {
		let t = [];
		return [t, function(e, s=1) {
			a(0, t = [e, ...t]),
			setTimeout((() => a(0, t = t.slice(0, t.length - 1))), 1e3 * s)
		}]
	}
	class Ct extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, qt, jt, n, {
				pop: 1
			})
		}
		get pop()
		{
			return this.$$.ctx[1]
		}
	}
	function St(s) {
		let a,
			t,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m,
			h,
			y,
			g,
			f,
			b,
			k,
			w = s[0] + 1 + "",
			v = s[1].length + "",
			x = s[1][s[0]] + "";
		return {
			c() {
				a = S("div"),
				t = S("div"),
				r = M("Tip "),
				n = M(w),
				i = M("/"),
				l = M(v),
				u = T(),
				c = S("div"),
				d = M(x),
				p = T(),
				m = N("svg"),
				h = N("path"),
				y = T(),
				g = N("svg"),
				f = N("path"),
				A(t, "class", "number svelte-ksmmv8"),
				A(c, "class", "tip svelte-ksmmv8"),
				A(h, "d", "M75,0L25,50L75,100z"),
				A(m, "class", "left svelte-ksmmv8"),
				A(m, "xmlns", "http://www.w3.org/2000/svg"),
				A(m, "viewBox", "0 0 100 100"),
				A(f, "d", "M25,0L75,50L25,100z"),
				A(g, "class", "right svelte-ksmmv8"),
				A(g, "xmlns", "http://www.w3.org/2000/svg"),
				A(g, "viewBox", "0 0 100 100"),
				A(a, "class", "outer svelte-ksmmv8")
			},
			m(e, o) {
				j(e, a, o),
				$(a, t),
				$(t, r),
				$(t, n),
				$(t, i),
				$(t, l),
				$(a, u),
				$(a, c),
				$(c, d),
				$(a, p),
				$(a, m),
				$(m, h),
				$(a, y),
				$(a, g),
				$(g, f),
				b || (k = [E(m, "click", s[3]), E(g, "click", s[4])], b = !0)
			},
			p(e, [s]) {
				1 & s && w !== (w = e[0] + 1 + "") && H(n, w),
				1 & s && x !== (x = e[1][e[0]] + "") && H(d, x)
			},
			i: e,
			o: e,
			d(e) {
				e && q(a),
				b = !1,
				o(k)
			}
		}
	}
	function Nt(e, s, a) {
		let {index: t=0} = s;
		const o = ["You can change the gamemode by clicking wordle+.", "Hard mode is game mode specific. Turning it on in one game mode won't change it on the others.", "Double tap or right click a word on the board to learn its definition.", "Hard mode can be enabled during a game if you haven't violated the hard mode rules yet.", "Double tap or right click the next row to see how many possible words can be played there, if you use all the previous information.", "Because words are chosen from the list randomly it is possible to get the same word again.", "When you see the refresh button in the top left corner it means a new word is ready.", "Everyone has the same wordle at the same time. Your word #73 is the same as everyone elses #73.", "There are more valid guesses than possible words, ie. not all 5 letter words can be selected as an answer by the game.", "Historical games don't count towards your stats. Historical games are when you follow a link to a specific game number."],
			r = o.length;
		return e.$$set = e => {
			"index" in e && a(0, t = e.index)
		}, [t, o, r, () => a(0, t = (t - 1 + o.length) % o.length), () => a(0, t = (t + 1) % o.length)]
	}
	class Mt extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Nt, St, n, {
				index: 0,
				length: 2
			})
		}
		get length()
		{
			return this.$$.ctx[2]
		}
	}
	function Tt(s) {
		let a;
		return {
			c() {
				a = N("path"),
				A(a, "d", "M4.167 4.167c-1.381 1.381-1.381 3.619 0 5L6.5 11.5a1.18 1.18 0 0 1 0 1.667 1.18 1.18 0 0 1-1.667 0L2.5 10.833C.199 8.532.199 4.801 2.5 2.5s6.032-2.301 8.333 0l3.333 3.333c2.301 2.301 2.301 6.032 0 8.333a1.18 1.18 0 0 1-1.667 0 1.18 1.18 0 0 1 0-1.667c1.381-1.381 1.381-3.619 0-5L9.167 4.167c-1.381-1.381-3.619-1.381-5 0zm5.667 14c-2.301-2.301-2.301-6.032 0-8.333a1.18 1.18 0 0 1 1.667 0 1.18 1.18 0 0 1 0 1.667c-1.381 1.381-1.381 3.619 0 5l3.333 3.333c1.381 1.381 3.619 1.381 5 0s1.381-3.619 0-5L17.5 12.5a1.18 1.18 0 0 1 0-1.667 1.18 1.18 0 0 1 1.667 0l2.333 2.333c2.301 2.301 2.301 6.032 0 8.333s-6.032 2.301-8.333 0l-3.333-3.333z")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Ot(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d = ts.modes[e[1]].name + "";
		return a = new $s({
			props: {
				$$slots: {
					default: [Tt]
				},
				$$scope: {
					ctx: e
				}
			}
		}), {
			c() {
				s = S("div"),
				qe(a.$$.fragment),
				t = M("\n\tCopy link to this game ("),
				o = M(d),
				r = M(" #"),
				n = M(e[0]),
				i = M(")"),
				A(s, "class", "svelte-qtlar2")
			},
			m(d, p) {
				j(d, s, p),
				Ce(a, s, null),
				$(s, t),
				$(s, o),
				$(s, r),
				$(s, n),
				$(s, i),
				l = !0,
				u || (c = E(s, "click", e[2]), u = !0)
			},
			p(e, [s]) {
				const t = {};
				16 & s && (t.$$scope = {
					dirty: s,
					ctx: e
				}),
				a.$set(t),
				(!l || 2 & s) && d !== (d = ts.modes[e[1]].name + "") && H(o, d),
				(!l || 1 & s) && H(n, e[0])
			},
			i(e) {
				l || (ke(a.$$.fragment, e), l = !0)
			},
			o(e) {
				we(a.$$.fragment, e),
				l = !1
			},
			d(e) {
				e && q(s),
				Se(a),
				u = !1,
				c()
			}
		}
	}
	function Et(e, s, a) {
		let t;
		i(e, fs, (e => a(1, t = e)));
		let {wordNumber: o} = s;
		const r = Q("toaster");
		return e.$$set = e => {
			"wordNumber" in e && a(0, o = e.wordNumber)
		}, [o, t, function() {
			r.pop("Copied"),
			navigator.clipboard.writeText(`${window.location.href}/${o}`)
		}]
	}
	class _t extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Et, Ot, n, {
				wordNumber: 0
			})
		}
	}
	function Lt(s) {
		let a,
			t,
			o,
			r,
			n,
			i;
		return {
			c() {
				a = S("section"),
				t = S("div"),
				o = M(s[0]),
				r = T(),
				n = S("div"),
				i = M(s[1]),
				A(t, "class", "stat svelte-dvu5v6"),
				A(n, "class", "name svelte-dvu5v6"),
				A(a, "class", "svelte-dvu5v6")
			},
			m(e, s) {
				j(e, a, s),
				$(a, t),
				$(t, o),
				$(a, r),
				$(a, n),
				$(n, i)
			},
			p(e, [s]) {
				1 & s && H(o, e[0]),
				2 & s && H(i, e[1])
			},
			i: e,
			o: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function At(e, s, a) {
		let {stat: t} = s,
			{name: o} = s;
		return e.$$set = e => {
			"stat" in e && a(0, t = e.stat),
			"name" in e && a(1, o = e.name)
		}, [t, o]
	}
	class Ht extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, At, Lt, n, {
				stat: 0,
				name: 1
			})
		}
	}
	function It(e, s, a) {
		const t = e.slice();
		return t[3] = s[a], t
	}
	function Dt(e) {
		let s,
			a;
		return s = new Ht({
			props: {
				name: e[3][0],
				stat: e[3][1]
			}
		}), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				1 & a && (t.name = e[3][0]),
				1 & a && (t.stat = e[3][1]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function Rt(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l = ts.modes[e[1]].name + "",
			u = e[0],
			c = [];
		for (let s = 0; s < u.length; s += 1)
			c[s] = Dt(It(e, u, s));
		const d = e => we(c[e], 1, 1, (() => {
			c[e] = null
		}));
		return {
			c() {
				s = S("h3"),
				a = M("Statistics ("),
				t = M(l),
				o = M(")"),
				r = T(),
				n = S("div");
				for (let e = 0; e < c.length; e += 1)
					c[e].c();
				A(n, "class", "svelte-ljn64v")
			},
			m(e, l) {
				j(e, s, l),
				$(s, a),
				$(s, t),
				$(s, o),
				j(e, r, l),
				j(e, n, l);
				for (let e = 0; e < c.length; e += 1)
					c[e].m(n, null);
				i = !0
			},
			p(e, [s]) {
				if ((!i || 2 & s) && l !== (l = ts.modes[e[1]].name + "") && H(t, l), 1 & s) {
					let a;
					for (u = e[0], a = 0; a < u.length; a += 1) {
						const t = It(e, u, a);
						c[a] ? (c[a].p(t, s), ke(c[a], 1)) : (c[a] = Dt(t), c[a].c(), ke(c[a], 1), c[a].m(n, null))
					}
					for (fe(), a = u.length; a < c.length; a += 1)
						d(a);
					be()
				}
			},
			i(e) {
				if (!i) {
					for (let e = 0; e < u.length; e += 1)
						ke(c[e]);
					i = !0
				}
			},
			o(e) {
				c = c.filter(Boolean);
				for (let e = 0; e < c.length; e += 1)
					we(c[e]);
				i = !1
			},
			d(e) {
				e && q(s),
				e && q(r),
				e && q(n),
				C(c, e)
			}
		}
	}
	function Ut(e, s, a) {
		let t;
		i(e, fs, (e => a(1, t = e)));
		let o,
			{data: r} = s;
		return e.$$set = e => {
			"data" in e && a(2, r = e.data)
		}, e.$$.update = () => {
			5 & e.$$.dirty && (a(0, o = [["Played", r.played], ["Win %", Math.round((r.played - r.guesses.fail) / r.played * 100) || 0], ["Average Guesses", (Object.entries(r.guesses).reduce(((e, s) => isNaN(parseInt(s[0])) ? e : e + parseInt(s[0]) * s[1]), 0) / r.played || 0).toFixed(1)]]), r.guesses.fail > 0 && o.push(["Lost", r.guesses.fail]), "streak" in r && (o.push(["Current Streak", r.streak]), o.push(["Max Streak", r.maxStreak])))
		}, [o, t, r]
	}
	class Bt extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Ut, Rt, n, {
				data: 2
			})
		}
	}
	function Wt(e, s, a) {
		const t = e.slice();
		t[3] = s[a],
		t[6] = a;
		const o = Number(t[3][0]);
		return t[4] = o, t
	}
	function Gt(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l = e[3][0] + "",
			u = e[3][1] + "";
		return {
			c() {
				s = S("div"),
				a = S("span"),
				t = M(l),
				o = T(),
				r = S("div"),
				n = M(u),
				i = T(),
				A(a, "class", "guess svelte-1pserw8"),
				A(r, "class", "bar svelte-1pserw8"),
				I(r, "width", e[3][1] / e[2] * 100 + "%"),
				R(r, "this", e[4] === e[0].guesses && !e[0].active && !ds(e[0])),
				A(s, "class", "graph svelte-1pserw8")
			},
			m(e, l) {
				j(e, s, l),
				$(s, a),
				$(a, t),
				$(s, o),
				$(s, r),
				$(r, n),
				$(s, i)
			},
			p(e, s) {
				2 & s && l !== (l = e[3][0] + "") && H(t, l),
				2 & s && u !== (u = e[3][1] + "") && H(n, u),
				6 & s && I(r, "width", e[3][1] / e[2] * 100 + "%"),
				3 & s && R(r, "this", e[4] === e[0].guesses && !e[0].active && !ds(e[0]))
			},
			d(e) {
				e && q(s)
			}
		}
	}
	function Pt(e, s) {
		let a,
			t,
			o = !isNaN(s[4]),
			r = o && Gt(s);
		return {
			key: e,
			first: null,
			c() {
				a = O(),
				r && r.c(),
				t = O(),
				this.first = a
			},
			m(e, s) {
				j(e, a, s),
				r && r.m(e, s),
				j(e, t, s)
			},
			p(e, a) {
				s = e,
				2 & a && (o = !isNaN(s[4])),
				o ? r ? r.p(s, a) : (r = Gt(s), r.c(), r.m(t.parentNode, t)) : r && (r.d(1), r = null)
			},
			d(e) {
				e && q(a),
				r && r.d(e),
				e && q(t)
			}
		}
	}
	function Jt(s) {
		let a,
			t,
			o,
			r = [],
			n = new Map,
			i = Object.entries(s[1]);
		const l = e => e[3][0];
		for (let e = 0; e < i.length; e += 1) {
			let a = Wt(s, i, e),
				t = l(a);
			n.set(t, r[e] = Pt(t, a))
		}
		return {
			c() {
				a = S("h3"),
				a.textContent = "guess distribution",
				t = T(),
				o = S("div");
				for (let e = 0; e < r.length; e += 1)
					r[e].c();
				A(o, "class", "container svelte-1pserw8")
			},
			m(e, s) {
				j(e, a, s),
				j(e, t, s),
				j(e, o, s);
				for (let e = 0; e < r.length; e += 1)
					r[e].m(o, null)
			},
			p(e, [s]) {
				7 & s && (i = Object.entries(e[1]), r = function(e, s, a, t, o, r, n, i, l, u, c, d) {
					let p = e.length,
						m = r.length,
						h = p;
					const y = {};
					for (; h--;)
						y[e[h].key] = h;
					const g = [],
						f = new Map,
						b = new Map;
					for (h = m; h--;) {
						const e = d(o, r, h),
							i = a(e);
						let l = n.get(i);
						l ? t && l.p(e, s) : (l = u(i, e), l.c()),
						f.set(i, g[h] = l),
						i in y && b.set(i, Math.abs(h - y[i]))
					}
					const k = new Set,
						w = new Set;
					function v(e) {
						ke(e, 1),
						e.m(i, c),
						n.set(e.key, e),
						c = e.first,
						m--
					}
					for (; p && m;) {
						const s = g[m - 1],
							a = e[p - 1],
							t = s.key,
							o = a.key;
						s === a ? (c = s.first, p--, m--) : f.has(o) ? !n.has(t) || k.has(t) ? v(s) : w.has(o) ? p-- : b.get(t) > b.get(o) ? (w.add(t), v(s)) : (k.add(o), p--) : (l(a, n), p--)
					}
					for (; p--;) {
						const s = e[p];
						f.has(s.key) || l(s, n)
					}
					for (; m;)
						v(g[m - 1]);
					return g
				}(r, s, l, 1, e, i, n, o, ze, Pt, null, Wt))
			},
			i: e,
			o: e,
			d(e) {
				e && q(a),
				e && q(t),
				e && q(o);
				for (let e = 0; e < r.length; e += 1)
					r[e].d()
			}
		}
	}
	function Yt(e, s, a) {
		let t,
			{game: o} = s,
			{distribution: r} = s;
		return e.$$set = e => {
			"game" in e && a(0, o = e.game),
			"distribution" in e && a(1, r = e.distribution)
		}, e.$$.update = () => {
			2 & e.$$.dirty && a(2, t = Object.entries(r).reduce(((e, s) => isNaN(Number(s[0])) ? e : Math.max(s[1], e)), 1))
		}, [o, r, t]
	}
	class Vt extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, Yt, Jt, n, {
				game: 0,
				distribution: 1
			})
		}
	}
	function Xt(e) {
		let s,
			a;
		return s = new ft({
			props: {
				visible: e[7]
			}
		}), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				128 & a[0] && (t.visible = e[7]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function Ft(e) {
		let s,
			a,
			t,
			o;
		return s = new Bt({
			props: {
				data: e[1]
			}
		}), t = new Vt({
			props: {
				distribution: e[1].guesses,
				game: e[2]
			}
		}), {
			c() {
				qe(s.$$.fragment),
				a = T(),
				qe(t.$$.fragment)
			},
			m(e, r) {
				Ce(s, e, r),
				j(e, a, r),
				Ce(t, e, r),
				o = !0
			},
			p(e, a) {
				const o = {};
				2 & a[0] && (o.data = e[1]),
				s.$set(o);
				const r = {};
				2 & a[0] && (r.distribution = e[1].guesses),
				4 & a[0] && (r.game = e[2]),
				t.$set(r)
			},
			i(e) {
				o || (ke(s.$$.fragment, e), ke(t.$$.fragment, e), o = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				we(t.$$.fragment, e),
				o = !1
			},
			d(e) {
				Se(s, e),
				e && q(a),
				Se(t, e)
			}
		}
	}
	function Kt(s) {
		let a;
		return {
			c() {
				a = S("h2"),
				a.textContent = "Statistics not available for historical games",
				A(a, "class", "historical svelte-1ixvu6x")
			},
			m(e, s) {
				j(e, a, s)
			},
			p: e,
			i: e,
			o: e,
			d(e) {
				e && q(a)
			}
		}
	}
	function Zt(e) {
		let s,
			a;
		return s = new $t({
			props: {
				slot: "1"
			}
		}), e[32](s), s.$on("timeup", e[33]), s.$on("reload", e[18]), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				s.$set({})
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(a) {
				e[32](null),
				Se(s, a)
			}
		}
	}
	function Qt(e) {
		let s,
			a;
		return s = new ht({
			props: {
				slot: "2",
				state: e[2]
			}
		}), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				4 & a[0] && (t.state = e[2]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function eo(a) {
		let t,
			o,
			n,
			i;
		return {
			c() {
				t = S("div"),
				t.textContent = "give up",
				A(t, "class", "concede svelte-1ixvu6x")
			},
			m(e, s) {
				j(e, t, s),
				n || (i = E(t, "click", a[17]), n = !0)
			},
			p: e,
			i(a) {
				o || ne((() => {
					o = function(a, t, o) {
						let n,
							i,
							l = t(a, o),
							u = !1,
							c = 0;
						function d() {
							n && J(a, n)
						}
						function p() {
							const {delay: t=0, duration: o=300, easing: r=s, tick: p=e, css: m} = l || ve;
							m && (n = P(a, 0, 1, o, t, r, m, c++)),
							p(0, 1);
							const h = f() + t,
								y = h + o;
							i && i.abort(),
							u = !0,
							ne((() => he(a, !0, "start"))),
							i = v((e => {
								if (u) {
									if (e >= y)
										return p(1, 0), he(a, !0, "end"), d(), u = !1;
									if (e >= h) {
										const s = r((e - h) / o);
										p(s, 1 - s)
									}
								}
								return u
							}))
						}
						let m = !1;
						return {
							start() {
								m || (m = !0, J(a), r(l) ? (l = l(), me().then(p)) : p())
							},
							invalidate() {
								m = !1
							},
							end() {
								u && (d(), u = !1)
							}
						}
					}(t, ms, {
						delay: 300
					}),
					o.start()
				}))
			},
			o: e,
			d(e) {
				e && q(t),
				n = !1,
				i()
			}
		}
	}
	function so(e) {
		let s,
			a;
		return s = new Ks({
			props: {
				word: e[0],
				alternates: 2
			}
		}), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, t) {
				Ce(s, e, t),
				a = !0
			},
			p(e, a) {
				const t = {};
				1 & a[0] && (t.word = e[0]),
				s.$set(t)
			},
			i(e) {
				a || (ke(s.$$.fragment, e), a = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				a = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function ao(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d;
		const p = [Kt, Ft],
			m = [];
		function h(e, s) {
			return e[6].modes[e[13]].historical ? 0 : 1
		}
		s = h(e),
		a = m[s] = p[s](e),
		o = new dt({
			props: {
				visible: !e[2].active,
				$$slots: {
					2: [Qt],
					1: [Zt]
				},
				$$scope: {
					ctx: e
				}
			}
		}),
		n = new _t({
			props: {
				wordNumber: e[2].wordNumber
			}
		});
		const y = [so, eo],
			g = [];
		function f(e, s) {
			return e[2].active ? 1 : 0
		}
		return l = f(e), u = g[l] = y[l](e), {
			c() {
				a.c(),
				t = T(),
				qe(o.$$.fragment),
				r = T(),
				qe(n.$$.fragment),
				i = T(),
				u.c(),
				c = O()
			},
			m(e, a) {
				m[s].m(e, a),
				j(e, t, a),
				Ce(o, e, a),
				j(e, r, a),
				Ce(n, e, a),
				j(e, i, a),
				g[l].m(e, a),
				j(e, c, a),
				d = !0
			},
			p(e, r) {
				let i = s;
				s = h(e),
				s === i ? m[s].p(e, r) : (fe(), we(m[i], 1, 1, (() => {
					m[i] = null
				})), be(), a = m[s], a ? a.p(e, r) : (a = m[s] = p[s](e), a.c()), ke(a, 1), a.m(t.parentNode, t));
				const d = {};
				4 & r[0] && (d.visible = !e[2].active),
				2564 & r[0] | 4096 & r[1] && (d.$$scope = {
					dirty: r,
					ctx: e
				}),
				o.$set(d);
				const b = {};
				4 & r[0] && (b.wordNumber = e[2].wordNumber),
				n.$set(b);
				let k = l;
				l = f(e),
				l === k ? g[l].p(e, r) : (fe(), we(g[k], 1, 1, (() => {
					g[k] = null
				})), be(), u = g[l], u ? u.p(e, r) : (u = g[l] = y[l](e), u.c()), ke(u, 1), u.m(c.parentNode, c))
			},
			i(e) {
				d || (ke(a), ke(o.$$.fragment, e), ke(n.$$.fragment, e), ke(u), d = !0)
			},
			o(e) {
				we(a),
				we(o.$$.fragment, e),
				we(n.$$.fragment, e),
				we(u),
				d = !1
			},
			d(e) {
				m[s].d(e),
				e && q(t),
				Se(o, e),
				e && q(r),
				Se(n, e),
				e && q(i),
				g[l].d(e),
				e && q(c)
			}
		}
	}
	function to(s) {
		let a,
			t,
			o;
		return {
			c() {
				a = S("div"),
				a.textContent = "give up",
				A(a, "class", "concede svelte-1ixvu6x")
			},
			m(e, r) {
				j(e, a, r),
				t || (o = E(a, "click", s[17]), t = !0)
			},
			p: e,
			d(e) {
				e && q(a),
				t = !1,
				o()
			}
		}
	}
	function oo(e) {
		let s,
			a,
			t,
			o,
			r;
		s = new ot({
			props: {
				state: e[2]
			}
		});
		let n = e[2].active && to(e),
			i = {
				index: e[12]
			};
		return o = new Mt({
			props: i
		}), e[36](o), {
			c() {
				qe(s.$$.fragment),
				a = T(),
				n && n.c(),
				t = T(),
				qe(o.$$.fragment)
			},
			m(e, i) {
				Ce(s, e, i),
				j(e, a, i),
				n && n.m(e, i),
				j(e, t, i),
				Ce(o, e, i),
				r = !0
			},
			p(e, a) {
				const r = {};
				4 & a[0] && (r.state = e[2]),
				s.$set(r),
				e[2].active ? n ? n.p(e, a) : (n = to(e), n.c(), n.m(t.parentNode, t)) : n && (n.d(1), n = null);
				const i = {};
				4096 & a[0] && (i.index = e[12]),
				o.$set(i)
			},
			i(e) {
				r || (ke(s.$$.fragment, e), ke(o.$$.fragment, e), r = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				we(o.$$.fragment, e),
				r = !1
			},
			d(r) {
				Se(s, r),
				r && q(a),
				n && n.d(r),
				r && q(t),
				e[36](null),
				Se(o, r)
			}
		}
	}
	function ro(e) {
		let s,
			a,
			t,
			o,
			r,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			m = e[6].modes[e[13]].name + "",
			h = e[2].wordNumber + "";
		return {
			c() {
				s = S("div"),
				a = S("a"),
				a.textContent = "Original Wordle",
				t = T(),
				o = S("div"),
				r = S("div"),
				r.textContent = `v${e[15]}`,
				n = T(),
				i = S("div"),
				l = M(m),
				u = M(" word #"),
				c = M(h),
				A(a, "href", "https://www.nytimes.com/games/wordle/"),
				A(a, "target", "_blank"),
				A(i, "title", "double click to reset your stats"),
				A(i, "class", "word"),
				A(s, "slot", "footer")
			},
			m(m, h) {
				j(m, s, h),
				$(s, a),
				$(s, t),
				$(s, o),
				$(o, r),
				$(o, n),
				$(o, i),
				$(i, l),
				$(i, u),
				$(i, c),
				d || (p = E(i, "dblclick", e[35]), d = !0)
			},
			p(e, s) {
				8256 & s[0] && m !== (m = e[6].modes[e[13]].name + "") && H(l, m),
				4 & s[0] && h !== (h = e[2].wordNumber + "") && H(c, h)
			},
			d(e) {
				e && q(s),
				d = !1,
				p()
			}
		}
	}
	function no(e) {
		let s,
			a,
			t,
			n,
			i,
			l,
			u,
			c,
			d,
			p,
			h,
			y,
			g,
			f,
			b,
			k,
			w,
			v,
			x,
			z,
			C,
			N;
		function M(s) {
			e[19](s)
		}
		let O = {
			tutorial: 2 === e[14].tutorial,
			showStats: e[1].played > 0 || e[6].modes[e[13]].historical && !e[2].active
		};
		function _(s) {
			e[25](s)
		}
		void 0 !== e[9] && (O.showRefresh = e[9]),
		t = new Os({
			props: O
		}),
		se.push((() => je(t, "showRefresh", M))),
		t.$on("closeTutPopUp", m(e[20])),
		t.$on("stats", e[21]),
		t.$on("tutorial", e[22]),
		t.$on("settings", e[23]),
		t.$on("reload", e[18]);
		let L = {
			tutorial: 1 === e[14].tutorial,
			board: e[2].board,
			guesses: e[2].guesses,
			icon: e[6].modes[e[13]].icon
		};
		function H(s) {
			e[27](s)
		}
		void 0 !== e[2].board.words && (L.value = e[2].board.words),
		l = new ca({
			props: L
		}),
		e[24](l),
		se.push((() => je(l, "value", _))),
		l.$on("closeTutPopUp", m(e[26]));
		let D = {
			disabled: !e[2].active || 3 === e[14].tutorial
		};
		function U(s) {
			e[30](s)
		}
		void 0 !== e[2].board.words[6 === e[2].guesses ? 0 : e[2].guesses] && (D.value = e[2].board.words[6 === e[2].guesses ? 0 : e[2].guesses]),
		d = new xa({
			props: D
		}),
		se.push((() => je(d, "value", H))),
		d.$on("keystroke", e[28]),
		d.$on("submitWord", e[16]),
		d.$on("esc", e[29]);
		let B = {
			fullscreen: 0 === e[14].tutorial,
			$$slots: {
				default: [Xt]
			},
			$$scope: {
				ctx: e
			}
		};
		function W(s) {
			e[34](s)
		}
		void 0 !== e[7] && (B.visible = e[7]),
		y = new Oa({
			props: B
		}),
		se.push((() => je(y, "visible", U))),
		y.$on("close", m(e[31]));
		let G = {
			$$slots: {
				default: [ao]
			},
			$$scope: {
				ctx: e
			}
		};
		function P(s) {
			e[37](s)
		}
		void 0 !== e[8] && (G.visible = e[8]),
		b = new Oa({
			props: G
		}),
		se.push((() => je(b, "visible", W)));
		let J = {
			fullscreen: !0,
			$$slots: {
				footer: [ro],
				default: [oo]
			},
			$$scope: {
				ctx: e
			}
		};
		return void 0 !== e[4] && (J.visible = e[4]), v = new Oa({
			props: J
		}), se.push((() => je(v, "visible", P))), {
			c() {
				s = T(),
				a = S("main"),
				qe(t.$$.fragment),
				i = T(),
				qe(l.$$.fragment),
				c = T(),
				qe(d.$$.fragment),
				h = T(),
				qe(y.$$.fragment),
				f = T(),
				qe(b.$$.fragment),
				w = T(),
				qe(v.$$.fragment),
				I(a, "--rows", 6),
				I(a, "--cols", 5),
				A(a, "class", "svelte-1ixvu6x"),
				R(a, "guesses", 0 !== e[2].guesses)
			},
			m(o, n) {
				j(o, s, n),
				j(o, a, n),
				Ce(t, a, null),
				$(a, i),
				Ce(l, a, null),
				$(a, c),
				Ce(d, a, null),
				j(o, h, n),
				Ce(y, o, n),
				j(o, f, n),
				Ce(b, o, n),
				j(o, w, n),
				Ce(v, o, n),
				z = !0,
				C || (N = [E(document.body, "click", (function() {
					r(e[10].hideCtx) && e[10].hideCtx.apply(this, arguments)
				})), E(document.body, "contextmenu", (function() {
					r(e[10].hideCtx) && e[10].hideCtx.apply(this, arguments)
				}))], C = !0)
			},
			p(s, o) {
				e = s;
				const r = {};
				16384 & o[0] && (r.tutorial = 2 === e[14].tutorial),
				8262 & o[0] && (r.showStats = e[1].played > 0 || e[6].modes[e[13]].historical && !e[2].active),
				!n && 512 & o[0] && (n = !0, r.showRefresh = e[9], ie((() => n = !1))),
				t.$set(r);
				const i = {};
				16384 & o[0] && (i.tutorial = 1 === e[14].tutorial),
				4 & o[0] && (i.board = e[2].board),
				4 & o[0] && (i.guesses = e[2].guesses),
				8256 & o[0] && (i.icon = e[6].modes[e[13]].icon),
				!u && 4 & o[0] && (u = !0, i.value = e[2].board.words, ie((() => u = !1))),
				l.$set(i);
				const c = {};
				16388 & o[0] && (c.disabled = !e[2].active || 3 === e[14].tutorial),
				!p && 4 & o[0] && (p = !0, c.value = e[2].board.words[6 === e[2].guesses ? 0 : e[2].guesses], ie((() => p = !1))),
				d.$set(c),
				4 & o[0] && R(a, "guesses", 0 !== e[2].guesses);
				const m = {};
				16384 & o[0] && (m.fullscreen = 0 === e[14].tutorial),
				128 & o[0] | 4096 & o[1] && (m.$$scope = {
					dirty: o,
					ctx: e
				}),
				!g && 128 & o[0] && (g = !0, m.visible = e[7], ie((() => g = !1))),
				y.$set(m);
				const h = {};
				10823 & o[0] | 4096 & o[1] && (h.$$scope = {
					dirty: o,
					ctx: e
				}),
				!k && 256 & o[0] && (k = !0, h.visible = e[8], ie((() => k = !1))),
				b.$set(h);
				const f = {};
				12396 & o[0] | 4096 & o[1] && (f.$$scope = {
					dirty: o,
					ctx: e
				}),
				!x && 16 & o[0] && (x = !0, f.visible = e[4], ie((() => x = !1))),
				v.$set(f)
			},
			i(e) {
				z || (ke(t.$$.fragment, e), ke(l.$$.fragment, e), ke(d.$$.fragment, e), ke(y.$$.fragment, e), ke(b.$$.fragment, e), ke(v.$$.fragment, e), z = !0)
			},
			o(e) {
				we(t.$$.fragment, e),
				we(l.$$.fragment, e),
				we(d.$$.fragment, e),
				we(y.$$.fragment, e),
				we(b.$$.fragment, e),
				we(v.$$.fragment, e),
				z = !1
			},
			d(r) {
				r && q(s),
				r && q(a),
				Se(t),
				e[24](null),
				Se(l),
				Se(d),
				r && q(h),
				Se(y, r),
				r && q(f),
				Se(b, r),
				r && q(w),
				Se(v, r),
				C = !1,
				o(N)
			}
		}
	}
	function io(e, s, a) {
		let t,
			o,
			r;
		i(e, fs, (e => a(13, t = e))),
		i(e, bs, (e => a(38, o = e))),
		i(e, ks, (e => a(14, r = e)));
		let {word: n} = s,
			{stats: l} = s,
			{game: u} = s,
			{toaster: c} = s;
		Z("toaster", c);
		const d = Q("version"),
			p = 2e3;
		let m,
			h,
			g,
			f = 3 === r.tutorial,
			b = !1,
			k = !1,
			w = !1,
			v = 0;
		function $() {
			a(2, u.active = !1, u),
			setTimeout(x, p),
			ts.modes[t].historical || (a(1, ++l.guesses.fail, l), a(1, ++l.played, l), "streak" in l && a(1, l.streak = 0, l), a(1, l.lastGame = ts.modes[t].seed, l), localStorage.setItem(`stats-${t}`, JSON.stringify(l)))
		}
		function x() {
			u.active || a(8, k = !0)
		}
		X((() => {
			u.active || setTimeout(x, p)
		}));
		return e.$$set = e => {
			"word" in e && a(0, n = e.word),
			"stats" in e && a(1, l = e.stats),
			"game" in e && a(2, u = e.game),
			"toaster" in e && a(3, c = e.toaster)
		}, e.$$.update = () => {
			48 & e.$$.dirty[0] && b && g && a(12, v = Math.floor(g.length * Math.random()))
		}, [n, l, u, c, b, g, ts, f, k, w, m, h, v, t, r, d, function() {
			if (5 !== u.board.words[u.guesses].length)
				c.pop("Not enough letters"),
				m.shake(u.guesses);
			else if (Ke.contains(u.board.words[u.guesses])) {
				if (u.guesses > 0) {
					const e = function(e, s) {
						for (let a = 0; a < 5; ++a)
							if ("🟩" === e.state[s - 1][a] && e.words[s - 1][a] !== e.words[s][a])
								return {
									pos: a,
									char: e.words[s - 1][a],
									type: "🟩"
								};
						for (let a = 0; a < 5; ++a)
							if ("🟨" === e.state[s - 1][a] && !e.words[s].includes(e.words[s - 1][a]))
								return {
									pos: a,
									char: e.words[s - 1][a],
									type: "🟨"
								};
						return {
							pos: -1,
							char: "",
							type: "⬛"
						}
					}(u.board, u.guesses);
					if (r.hard[t]) {
						if ("🟩" === e.type)
							return c.pop(`${function(e) {switch (e % 10) {case 1:return `${e}st`;case 2:return `${e}nd`;case 3:return `${e}rd`;default:return `${e}th`}}(e.pos + 1)} letter must be ${e.char.toUpperCase()}`), void m.shake(u.guesses);
						if ("🟨" === e.type)
							return c.pop(`Guess must contain ${e.char.toUpperCase()}`), void m.shake(u.guesses)
					} else
						"⬛" !== e.type && a(2, u.validHard = !1, u)
				}
				const e = function(e, s) {
					const a = e.split(""),
						t = Array(5).fill("⬛");
					for (let o = 0; o < e.length; ++o)
						a[o] === s.charAt(o) && (t[o] = "🟩", a[o] = "$");
					for (let o = 0; o < e.length; ++o) {
						const e = a.indexOf(s[o]);
						"🟩" !== t[o] && e >= 0 && (a[e] = "$", t[o] = "🟨")
					}
					return t
				}(n, u.board.words[u.guesses]);
				a(2, u.board.state[u.guesses] = e, u),
				e.forEach(((e, s) => {
					"🔳" !== o[u.board.words[u.guesses][s]] && "🟩" !== e || y(bs, o[u.board.words[u.guesses][s]] = e, o)
				})),
				a(2, ++u.guesses, u),
				u.board.words[u.guesses - 1] === n ? (m.bounce(u.guesses - 1), a(2, u.active = !1, u), setTimeout((() => c.pop(is[u.guesses - 1])), 1200), setTimeout(x, 2800), ts.modes[t].historical || (a(1, ++l.guesses[u.guesses], l), a(1, ++l.played, l), "streak" in l && (a(1, l.streak = ts.modes[t].seed - l.lastGame > ts.modes[t].unit ? 1 : l.streak + 1, l), l.streak > l.maxStreak && a(1, l.maxStreak = l.streak, l)), a(1, l.lastGame = ts.modes[t].seed, l), localStorage.setItem(`stats-${t}`, JSON.stringify(l)))) : 6 === u.guesses && $()
			} else
				c.pop("Not in word list"),
				m.shake(u.guesses)
		}, function() {
			a(4, b = !1),
			setTimeout(x, ns),
			$()
		}, function() {
			a(6, ts.modes[t].historical = !1, ts),
			a(6, ts.modes[t].seed = as(t), ts),
			a(2, u = ls(t)),
			a(0, n = Ke.words[rs(0, Ke.words.length, ts.modes[t].seed)]),
			y(bs, o = {
				a: "🔳",
				b: "🔳",
				c: "🔳",
				d: "🔳",
				e: "🔳",
				f: "🔳",
				g: "🔳",
				h: "🔳",
				i: "🔳",
				j: "🔳",
				k: "🔳",
				l: "🔳",
				m: "🔳",
				n: "🔳",
				o: "🔳",
				p: "🔳",
				q: "🔳",
				r: "🔳",
				s: "🔳",
				t: "🔳",
				u: "🔳",
				v: "🔳",
				w: "🔳",
				x: "🔳",
				y: "🔳",
				z: "🔳"
			}, o),
			a(8, k = !1),
			a(9, w = !1),
			h.reset(t)
		}, function(e) {
			w = e,
			a(9, w)
		}, () => y(ks, r.tutorial = 1, r), () => a(8, k = !0), () => a(7, f = !0), () => a(4, b = !0), function(e) {
			se[e ? "unshift" : "push"]((() => {
				m = e,
				a(10, m)
			}))
		}, function(s) {
			e.$$.not_equal(u.board.words, s) && (u.board.words = s, a(2, u))
		}, () => y(ks, r.tutorial = 0, r), function(s) {
			e.$$.not_equal(u.board.words[6 === u.guesses ? 0 : u.guesses], s) && (u.board.words[6 === u.guesses ? 0 : u.guesses] = s, a(2, u))
		}, () => {
			r.tutorial && y(ks, r.tutorial = 0, r),
			m.hideCtx()
		}, () => {
			a(7, f = !1),
			a(8, k = !1),
			a(4, b = !1)
		}, function(e) {
			f = e,
			a(7, f)
		}, () => 3 === r.tutorial && y(ks, --r.tutorial, r), function(e) {
			se[e ? "unshift" : "push"]((() => {
				h = e,
				a(11, h)
			}))
		}, () => a(9, w = !0), function(e) {
			k = e,
			a(8, k)
		}, () => {
			localStorage.clear(),
			c.pop("localStorage cleared")
		}, function(e) {
			se[e ? "unshift" : "push"]((() => {
				g = e,
				a(5, g)
			}))
		}, function(e) {
			b = e,
			a(4, b)
		}]
	}
	class lo extends Te {
		constructor(e)
		{
			super(),
			Me(this, e, io, no, n, {
				word: 0,
				stats: 1,
				game: 2,
				toaster: 3
			}, null, [-1, -1])
		}
	}
	function uo(e) {
		let s,
			a,
			t;
		function o(s) {
			e[6](s)
		}
		let r = {
			stats: e[1],
			word: e[2],
			toaster: e[3]
		};
		return void 0 !== e[0] && (r.game = e[0]), s = new lo({
			props: r
		}), se.push((() => je(s, "game", o))), {
			c() {
				qe(s.$$.fragment)
			},
			m(e, a) {
				Ce(s, e, a),
				t = !0
			},
			p(e, t) {
				const o = {};
				2 & t && (o.stats = e[1]),
				4 & t && (o.word = e[2]),
				8 & t && (o.toaster = e[3]),
				!a && 1 & t && (a = !0, o.game = e[0], ie((() => a = !1))),
				s.$set(o)
			},
			i(e) {
				t || (ke(s.$$.fragment, e), t = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				t = !1
			},
			d(e) {
				Se(s, e)
			}
		}
	}
	function co(e) {
		let s,
			a,
			t,
			o;
		s = new Ct({
			props: {}
		}),
		e[5](s);
		let r = e[3] && uo(e);
		return {
			c() {
				qe(s.$$.fragment),
				a = T(),
				r && r.c(),
				t = O()
			},
			m(e, n) {
				Ce(s, e, n),
				j(e, a, n),
				r && r.m(e, n),
				j(e, t, n),
				o = !0
			},
			p(e, [a]) {
				s.$set({}),
				e[3] ? r ? (r.p(e, a), 8 & a && ke(r, 1)) : (r = uo(e), r.c(), ke(r, 1), r.m(t.parentNode, t)) : r && (fe(), we(r, 1, 1, (() => {
					r = null
				})), be())
			},
			i(e) {
				o || (ke(s.$$.fragment, e), ke(r), o = !0)
			},
			o(e) {
				we(s.$$.fragment, e),
				we(r),
				o = !1
			},
			d(o) {
				e[5](null),
				Se(s, o),
				o && q(a),
				r && r.d(o),
				o && q(t)
			}
		}
	}
	function po(e, s, a) {
		let t;
		i(e, fs, (e => a(8, t = e)));
		let o,
			r,
			n,
			{version: l} = s;
		Z("version", l),
		localStorage.setItem("version", l),
		ks.set(JSON.parse(localStorage.getItem("settings")) || us()),
		ks.subscribe((e => localStorage.setItem("settings", JSON.stringify(e))));
		const u = window.location.hash.slice(1).split("/"),
			c = isNaN(Ye[u[0]]) ? parseInt(localStorage.getItem("mode")) || ts.default : Ye[u[0]];
		let d;
		return fs.set(c), !isNaN(parseInt(u[1])) && parseInt(u[1]) < os(c) && (ts.modes[c].seed = (parseInt(u[1]) - 1) * ts.modes[c].unit + ts.modes[c].start, ts.modes[c].historical = !0), fs.subscribe((e => {
			let s;
			localStorage.setItem("mode", `${e}`),
			window.location.hash = Ye[e],
			a(1, o = JSON.parse(localStorage.getItem(`stats-${e}`)) || function(e) {
				const s = {
					played: 0,
					lastGame: 0,
					guesses: {
						fail: 0,
						1: 0,
						2: 0,
						3: 0,
						4: 0,
						5: 0,
						6: 0
					}
				};
				return ts.modes[e].streak ? Object.assign(Object.assign({}, s), {
					streak: 0,
					maxStreak: 0
				}) : s
			}(e)),
			a(2, r = Ke.words[rs(0, Ke.words.length, ts.modes[e].seed)]),
			!0 === ts.modes[e].historical ? (s = JSON.parse(localStorage.getItem(`state-${e}-h`)), s && s.wordNumber === os(e) ? a(0, n = s) : a(0, n = ls(e))) : (s = JSON.parse(localStorage.getItem(`state-${e}`)), !s || ts.modes[e].seed - s.time >= ts.modes[e].unit ? a(0, n = ls(e)) : (s.wordNumber || (s.wordNumber = os(e)), a(0, n = s)));
			const t = {
				a: "🔳",
				b: "🔳",
				c: "🔳",
				d: "🔳",
				e: "🔳",
				f: "🔳",
				g: "🔳",
				h: "🔳",
				i: "🔳",
				j: "🔳",
				k: "🔳",
				l: "🔳",
				m: "🔳",
				n: "🔳",
				o: "🔳",
				p: "🔳",
				q: "🔳",
				r: "🔳",
				s: "🔳",
				t: "🔳",
				u: "🔳",
				v: "🔳",
				w: "🔳",
				x: "🔳",
				y: "🔳",
				z: "🔳"
			};
			for (let e = 0; e < 6; ++e)
				for (let s = 0; s < n.board.words[e].length; ++s)
					"🔳" !== t[n.board.words[e][s]] && "🟩" !== n.board.state[e][s] || (t[n.board.words[e][s]] = n.board.state[e][s]);
			bs.set(t)
		})), document.title = "Wordle+ | An infinite word guessing game", e.$$set = e => {
			"version" in e && a(4, l = e.version)
		}, e.$$.update = () => {
			1 & e.$$.dirty && function(e) {
				ts.modes[t].historical ? localStorage.setItem(`state-${t}-h`, JSON.stringify(e)) : localStorage.setItem(`state-${t}`, JSON.stringify(e))
			}(n)
		}, [n, o, r, d, l, function(e) {
			se[e ? "unshift" : "push"]((() => {
				d = e,
				a(3, d)
			}))
		}, function(e) {
			n = e,
			a(0, n)
		}]
	}
	return new class  extends Te{
		constructor(e)
		{
			super(),
			Me(this, e, po, co, n, {
				version: 4
			})
		}
	}

	(//! IF ANYTHING IN THIS FILE IS CHANGED MAKE SURE setVersion.js HAS ALSO BEEN UPDATED
	{
		target: document.body,
		props: {
			version: "1.3.2"
		}
	})
}();
//# sourceMappingURL=bundle.js.map
