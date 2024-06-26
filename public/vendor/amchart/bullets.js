am4internal_webpackJsonp(["2ff7"], {
    V3Xd: function (e, t, i) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: !0});
        var r = {};
        i.d(r, "PointedCircle", function () {
            return c
        }), i.d(r, "PinBullet", function () {
            return g
        }), i.d(r, "FlagBullet", function () {
            return m
        }), i.d(r, "Star", function () {
            return C
        }), i.d(r, "ShapeBullet", function () {
            return w
        });
        var n = i("m4/l"), a = i("1qam"), s = i("aCit"), o = i("hGwe"), l = i("Gg2j"), c = function (e) {
            function t() {
                var t = e.call(this) || this;
                return t.className = "PointedCircle", t.element = t.paper.add("path"), t.radius = 18, t.pointerAngle = 90, t.applyTheme(), t
            }

            return Object(n.c)(t, e), t.prototype.draw = function () {
                e.prototype.draw.call(this);
                var t = this.pointerBaseWidth, i = this.pointerLength;
                i <= .001 && (i = .001);
                var r = this.pointerAngle + 180, n = this.radius;
                t > 2 * n && (t = 2 * n);
                var a = this.pointerX, s = this.pointerY, c = o.moveTo({x: a, y: a}),
                    h = l.DEGREES * Math.atan(t / 2 / i);
                h <= .001 && (h = .001);
                var p = r - h, u = r + h;
                c += o.lineTo({x: a + i * l.cos(p), y: s + i * l.sin(p)}), c += o.arcToPoint({
                    x: a + i * l.cos(u),
                    y: s + i * l.sin(u)
                }, n, n, !0, !0), c += o.lineTo({x: a, y: a}), this.path = c
            }, Object.defineProperty(t.prototype, "radius", {
                get: function () {
                    return this.getPropertyValue("radius")
                }, set: function (e) {
                    this.setPropertyValue("radius", e, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "pointerAngle", {
                get: function () {
                    return this.getPropertyValue("pointerAngle")
                }, set: function (e) {
                    this.setPropertyValue("pointerAngle", e, !0)
                }, enumerable: !0, configurable: !0
            }), t.prototype.getTooltipY = function () {
                return l.sin(this.pointerAngle) * (.8 * -this.pointerLength - this.radius) - this.radius
            }, t.prototype.getTooltipX = function () {
                return l.cos(this.pointerAngle) * (.8 * -this.pointerLength - this.radius)
            }, t
        }(a.a);
        s.c.registeredClasses.PointerCircle = c;
        var h = i("TXRX"), p = i("FzPm"), u = i("tjMS"), d = i("MIZb"), g = function (e) {
            function t() {
                var t = e.call(this) || this;
                t.className = "PinBullet";
                var i = new d.a, r = t.createChild(p.a);
                r.shouldClone = !1, r.isMeasured = !1, r.fill = i.getFor("background"), r.radius = Object(u.c)(85), t.circle = r;
                var n = t.background;
                return n.fill = i.getFor("alternativeBackground"), n.fillOpacity = 1, n.pointerBaseWidth = 20, n.pointerLength = 20, n.radius = 25, n.events.on("propertychanged", t.invalidate, t, !1), t.applyTheme(), t
            }

            return Object(n.c)(t, e), t.prototype.validate = function () {
                e.prototype.validate.call(this);
                var t = this.background, i = t.pointerX, r = t.pointerY, n = t.pointerLength, a = t.pointerBaseWidth,
                    s = t.pointerAngle + 180, o = t.radius;
                a > 2 * o && (a = 2 * o);
                var c = l.DEGREES * Math.atan(a / 2 / n);
                c <= .001 && (c = .001);
                var h = s - c, p = s + c, d = {x: i + n * l.cos(h), y: r + n * l.sin(h)},
                    g = {x: i + n * l.cos(p), y: r + n * l.sin(p)}, y = d.x, f = g.x, b = d.y, m = g.y, v = o * o,
                    P = Math.sqrt((f - y) * (f - y) + (m - b) * (m - b)),
                    x = (y + f) / 2 - Math.sqrt(v - P / 2 * (P / 2)) * ((b - m) / P),
                    C = (b + m) / 2 - Math.sqrt(v - P / 2 * (P / 2)) * ((f - y) / P);
                this.circle && (this.circle.radius instanceof u.a && (this.circle.width = 2 * o, this.circle.height = 2 * o));
                var O = this.image;
                O ? (O.x = x, O.y = C, O.width = 2 * o, O.height = 2 * o, O.element.attr({preserveAspectRatio: "xMidYMid slice"}), this.circle && (this.circle.scale = 1 / O.scale)) : this.circle && (this.circle.x = x, this.circle.y = C);
                var V = this.label;
                V && (V.x = x, V.y = C)
            }, Object.defineProperty(t.prototype, "image", {
                get: function () {
                    return this._image
                }, set: function (e) {
                    e && (this._image = e, this._disposers.push(e), e.shouldClone = !1, e.parent = this, e.horizontalCenter = "middle", e.verticalCenter = "middle", this.circle && (e.mask = this.circle))
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "label", {
                get: function () {
                    return this._label
                }, set: function (e) {
                    e && (this._label = e, this._disposers.push(e), e.shouldClone = !1, e.parent = this, e.horizontalCenter = "middle", e.verticalCenter = "middle", e.textAlign = "middle", e.dy = 2)
                }, enumerable: !0, configurable: !0
            }), t.prototype.copyFrom = function (t) {
                e.prototype.copyFrom.call(this, t), t.image && (this._image || (this.image = t.image.clone()), this._image.copyFrom(t.image)), this.circle && t.circle && this.circle.copyFrom(t.circle), t.label && (this._label || (this.label = t.label.clone()), this._label.copyFrom(t.label))
            }, t.prototype.createBackground = function () {
                return new c
            }, t
        }(h.a);
        s.c.registeredClasses.PinBullet = g;
        var y = i("p9TX"), f = i("w4m0"), b = i("PTiM"), m = function (e) {
            function t() {
                var t = e.call(this) || this;
                t.className = "FlagBullet";
                var i = t.background;
                i.fillOpacity = 1, i.events.on("propertychanged", t.invalidate, t, !1), i.waveHeight = 1.5, i.waveLength = 7, i.setWavedSides(!0, !1, !0, !1), i.strokeOpacity = 1;
                var r = new d.a;
                t.stroke = r.getFor("alternativeBackground"), t.pole = t.createChild(b.a), t.pole.strokeOpacity = 1, t.width = 22, t.height = 16;
                var n = new y.a;
                return n.padding(3, 5, 3, 5), n.dy = 1, n.events.on("propertychanged", t.invalidate, t, !1), n.events.on("positionchanged", t.invalidate, t, !1), n.strokeOpacity = 0, t.label = n, t.poleHeight = 10, t.applyTheme(), t
            }

            return Object(n.c)(t, e), t.prototype.validate = function () {
                e.prototype.validate.call(this), this.updateBackground();
                var t = this.background;
                this.pole.y1 = 0;
                var i = this.poleHeight, r = this.label, n = t.pixelHeight;
                i > 0 ? (this.pole.y2 = -i - n, r && (r.y = -i - n)) : (this.pole.y2 = -i + n, r && (r.y = -i)), r && "middle" == r.horizontalCenter && (this.pole.y2 = -i)
            }, t.prototype.updateBackground = function () {
                var e = this._background;
                if (e) {
                    var t = this.label;
                    t ? (e.x = t.maxLeft, e.width = t.measuredWidth, e.height = t.measuredHeight) : (e.width = Math.abs(this.maxRight - this.maxLeft), e.height = Math.abs(this.maxBottom - this.maxTop));
                    var i = this.poleHeight;
                    e.y = i > 0 ? -i - e.pixelHeight : -i
                }
            }, Object.defineProperty(t.prototype, "label", {
                get: function () {
                    return this._label
                }, set: function (e) {
                    e ? (this._label = e, this._disposers.push(e), e.parent = this, e.shouldClone = !1) : (this._label && this._label.dispose(), this._label = e, this.invalidate())
                }, enumerable: !0, configurable: !0
            }), t.prototype.copyFrom = function (t) {
                t.label && (this.label = t.label.clone()), t.pole && this.pole.copyFrom(t.pole), e.prototype.copyFrom.call(this, t)
            }, t.prototype.createBackground = function () {
                return new f.a
            }, Object.defineProperty(t.prototype, "poleHeight", {
                get: function () {
                    return this.getPropertyValue("poleHeight")
                }, set: function (e) {
                    this.setPropertyValue("poleHeight", e, !0)
                }, enumerable: !0, configurable: !0
            }), t
        }(h.a);
        s.c.registeredClasses.FlagBullet = m;
        var v = i("Vs7R"), P = i("Mtpk"), x = i("v9UT"), C = function (e) {
            function t() {
                var t = e.call(this) || this;
                return t.className = "Star", t.pointCount = 5, t.arc = 360, t.radius = 100, t.innerRadius = Object(u.c)(30), t.cornerRadius = 0, t.innerCornerRadius = 0, t.startAngle = -90, t.element = t.paper.add("path"), t.applyTheme(), t
            }

            return Object(n.c)(t, e), t.prototype.draw = function () {
                e.prototype.draw.call(this);
                var t = this.startAngle, i = this.arc, r = this.pointCount, n = this.radius, a = this.pixelInnerRadius,
                    s = this.cornerRadius;
                s > n - a && (s = n - a);
                var c = this.innerCornerRadius;
                c > n - s - a && (c = n - s - a);
                for (var h = i / r / 2, p = "", u = 0; u < r; u++) {
                    var d = t + u * i / r;
                    if (s > 0) {
                        var g = {x: a * l.cos(d - h), y: a * l.sin(d - h)}, y = {x: n * l.cos(d), y: n * l.sin(d)},
                            f = {x: a * l.cos(d + h), y: a * l.sin(d + h)}, b = l.getAngle(y, g), m = l.getAngle(y, f),
                            v = y.x + s * l.cos(b), P = y.y + s * l.sin(b), x = y.x + s * l.cos(m),
                            C = y.y + s * l.sin(m);
                        p += o.lineTo({x: v, y: P}), p += " Q" + y.x + "," + y.y + " " + x + "," + C
                    } else p += o.lineTo({x: n * l.cos(d), y: n * l.sin(d)});
                    if (d += h, c > 0) {
                        g = {x: n * l.cos(d - h), y: n * l.sin(d - h)}, y = {
                            x: a * l.cos(d),
                            y: a * l.sin(d)
                        }, f = {
                            x: n * l.cos(d + h),
                            y: n * l.sin(d + h)
                        }, b = l.getAngle(y, g), m = l.getAngle(y, f), v = y.x + c * l.cos(b), P = y.y + c * l.sin(b), x = y.x + c * l.cos(m), C = y.y + c * l.sin(m);
                        p += o.lineTo({x: v, y: P}), p += " Q" + y.x + "," + y.y + " " + x + "," + C
                    } else p += o.lineTo({x: a * l.cos(d), y: a * l.sin(d)})
                }
                this.arc < 360 && (p += o.lineTo({
                    x: 0,
                    y: 0
                })), p = (p += o.closePath()).replace("L", "M"), this.path = p
            }, Object.defineProperty(t.prototype, "startAngle", {
                get: function () {
                    return this.getPropertyValue("startAngle")
                }, set: function (e) {
                    this.setPropertyValue("startAngle", l.normalizeAngle(e), !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "arc", {
                get: function () {
                    return this.getPropertyValue("arc")
                }, set: function (e) {
                    P.isNumber(e) || (e = 360), this.setPropertyValue("arc", e, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "radius", {
                get: function () {
                    var e = this.getPropertyValue("radius");
                    return P.isNumber(e) || (e = 0), e
                }, set: function (e) {
                    this.setPropertyValue("radius", e, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "radiusY", {
                get: function () {
                    var e = this.getPropertyValue("radiusY");
                    return P.isNumber(e) || (e = this.radius), e
                }, set: function (e) {
                    this.setPropertyValue("radiusY", e, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "innerRadius", {
                get: function () {
                    return this.getPropertyValue("innerRadius")
                }, set: function (e) {
                    this.setPercentProperty("innerRadius", e, !0, !1, 10, !1)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "pixelInnerRadius", {
                get: function () {
                    return x.relativeToValue(this.innerRadius, this.radius)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "cornerRadius", {
                get: function () {
                    return this.getPropertyValue("cornerRadius")
                }, set: function (e) {
                    this.setPropertyValue("cornerRadius", e, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "innerCornerRadius", {
                get: function () {
                    return this.getPropertyValue("innerCornerRadius")
                }, set: function (e) {
                    this.setPropertyValue("innerCornerRadius", e, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "pointCount", {
                get: function () {
                    var e = this.getPropertyValue("pointCount");
                    return l.max(3, e)
                }, set: function (e) {
                    this.setPropertyValue("pointCount", e, !0)
                }, enumerable: !0, configurable: !0
            }), t
        }(v.a);
        s.c.registeredClasses.Star = C;
        var O = i("GtDR"), V = i("Rnbi"), w = function (e) {
            function t() {
                var t = e.call(this) || this;
                return t.className = "ShapeBullet", t.size = 10, t.applyTheme(), t
            }

            return Object(n.c)(t, e), t.prototype.draw = function () {
                switch (e.prototype.draw.call(this), this.shapeElement && this.shapeElement.dispose(), this.shape) {
                    case"square":
                        this.createSquare();
                        break;
                    case"diamond":
                        this.createSquare(45);
                        break;
                    case"circle":
                        this.createCircle();
                        break;
                    case"up":
                        this.createTriangle();
                        break;
                    case"right":
                        this.createTriangle(90);
                        break;
                    case"down":
                        this.createTriangle(180);
                        break;
                    case"left":
                        this.createTriangle(270)
                }
            }, t.prototype.processShape = function () {
                this.shapeElement.horizontalCenter = "middle", this.shapeElement.verticalCenter = "middle"
            }, t.prototype.createSquare = function (e) {
                void 0 === e && (e = 0);
                var t = this.createChild(O.a);
                t.width = this.size, t.height = this.size, t.rotation = e, this.shapeElement = t, this.processShape()
            }, t.prototype.createCircle = function (e) {
                void 0 === e && (e = 0);
                var t = this.createChild(p.a);
                t.radius = this.size / 2, this.shapeElement = t, this.processShape()
            }, t.prototype.createTriangle = function (e) {
                void 0 === e && (e = 0);
                var t = this.createChild(V.a);
                t.width = this.size, t.height = this.size, t.rotation = e, this.shapeElement = t, this.processShape()
            }, Object.defineProperty(t.prototype, "shape", {
                get: function () {
                    return this.getPropertyValue("shape")
                }, set: function (e) {
                    this.setPropertyValue("shape", e, !0) && this.invalidate()
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(t.prototype, "size", {
                get: function () {
                    var e = this.getPropertyValue("size");
                    return P.isNumber(e) || (e = 0), e
                }, set: function (e) {
                    this.setPropertyValue("size", e, !0) && (this.width = e, this.height = e, this.invalidate())
                }, enumerable: !0, configurable: !0
            }), t
        }(h.a);
        s.c.registeredClasses.ShapeBullet = w, window.am4plugins_bullets = r
    }
}, ["V3Xd"]);