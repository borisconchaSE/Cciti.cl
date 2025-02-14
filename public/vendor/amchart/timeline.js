am4internal_webpackJsonp(["3cd8"], {
    vlr4: function (t, e, i) {
        "use strict";
        Object.defineProperty(e, "__esModule", {value: !0});
        var n = {};
        i.d(n, "CurveChartDataItem", function () {
            return S
        }), i.d(n, "CurveChart", function () {
            return O
        }), i.d(n, "SerpentineChartDataItem", function () {
            return j
        }), i.d(n, "SerpentineChart", function () {
            return I
        }), i.d(n, "SpiralChartDataItem", function () {
            return X
        }), i.d(n, "SpiralChart", function () {
            return Y
        }), i.d(n, "CurveLineSeriesDataItem", function () {
            return l
        }), i.d(n, "CurveLineSeries", function () {
            return h
        }), i.d(n, "CurveColumnSeriesDataItem", function () {
            return D
        }), i.d(n, "CurveColumnSeries", function () {
            return B
        }), i.d(n, "CurveStepLineSeriesDataItem", function () {
            return F
        }), i.d(n, "CurveStepLineSeries", function () {
            return G
        }), i.d(n, "CurveColumn", function () {
            return M
        }), i.d(n, "CurveCursor", function () {
            return z
        }), i.d(n, "AxisRendererCurveX", function () {
            return m
        }), i.d(n, "AxisRendererCurveY", function () {
            return T
        });
        var o = i("m4/l"), s = i("0Mwj"), r = i("v36H"), a = i("aCit"), p = i("Vs7R"), u = i("Gg2j"), l = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveLineSeriesDataItem", e.applyTheme(), e
            }

            return Object(o.c)(e, t), e
        }(r.b), h = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveLineSeries", e.connectEnds = !1, e.bulletsContainer.mask = new p.a, e.topOffset = .2, e.bottomOffset = .2, e.applyTheme(), e
            }

            return Object(o.c)(e, t), e.prototype.createDataItem = function () {
                return new l
            }, e.prototype.getPoint = function (t, e, i, n, o, s, r) {
                s || (s = "valueX"), r || (r = "valueY");
                var a = this.yAxis.renderer,
                    p = u.fitToRange(this.yAxis.getY(t, i, o, r), -a.radius * (1 + this.bottomOffset), -a.innerRadius * (1 + this.topOffset)),
                    l = this.xAxis.getX(t, e, n, s), h = this.xAxis.getY(t, e, n, s),
                    c = this.xAxis.getAngle(t, e, n, s);
                return {x: l + p * u.cos(c), y: h + p * u.sin(c)}
            }, e.prototype.addPoints = function (t, e, i, n, o) {
                var s = this.getPoint(e, i, n, e.locations[i], e.locations[n]);
                s && t.push(s)
            }, e.prototype.getMaskPath = function () {
                var t = this.yAxis.renderer.getPositionRangePath(this.yAxis.start, this.yAxis.end),
                    e = this.bulletsContainer;
                return this.chart && this.chart.maskBullets ? (e.mask || (e.mask = new p.a), e.mask.path = t) : e.mask = void 0, t
            }, e.prototype.drawSegment = function (e, i, n) {
                this.connectEnds && (this.dataFields[this._xOpenField] || this.dataFields[this._yOpenField] || this.stacked) && (i.push(i[0]), n.length > 0 && n.unshift(n[n.length - 1])), t.prototype.drawSegment.call(this, e, i, n)
            }, Object.defineProperty(e.prototype, "connectEnds", {
                get: function () {
                    return this.getPropertyValue("connectEnds")
                }, set: function (t) {
                    this.setPropertyValue("connectEnds", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "topOffset", {
                get: function () {
                    return this.getPropertyValue("topOffset")
                }, set: function (t) {
                    this.setPropertyValue("topOffset", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "bottomOffset", {
                get: function () {
                    return this.getPropertyValue("bottomOffset")
                }, set: function (t) {
                    this.setPropertyValue("bottomOffset", t, !0)
                }, enumerable: !0, configurable: !0
            }), e.prototype.positionBulletReal = function (t, e, i) {
                var n = this.xAxis, o = this.yAxis;
                (e < n.start || e > n.end || i < o.start || i > o.end) && (t.visible = !1), t.moveTo(this.xAxis.renderer.positionToPoint(e, i))
            }, e.prototype.setXAxis = function (e) {
                t.prototype.setXAxis.call(this, e), this.updateRendererRefs()
            }, e.prototype.setYAxis = function (e) {
                t.prototype.setYAxis.call(this, e), this.updateRendererRefs()
            }, e.prototype.updateRendererRefs = function () {
                var t = this.xAxis.renderer, e = this.yAxis.renderer;
                t.axisRendererY = e, e.axisRendererX = t
            }, e
        }(r.a);
        a.c.registeredClasses.CurveLineSeries = h, a.c.registeredClasses.CurveLineSeriesDataItem = l;
        var c = i("C6dT"), d = i("k6kv"), y = i("xgTw"), f = i("hGwe"), x = i("Mtpk"), g = i("hJ5i"), v = i("5xph"),
            P = i("aFzC"), m = function (t) {
                function e() {
                    var e = t.call(this) || this;
                    return e.pixelRadiusReal = 0, e.autoScaleScale = 1, e.layout = "none", e.autoScale = !0, e.autoCenter = !0, e.isMeasured = !1, e.className = "AxisRendererCurveX", e.line.strokeOpacity = 1, e.precisionStep = 10, e.points = [{
                        x: -300,
                        y: 0
                    }, {x: 300, y: 0}], e._tempSprite = e.createChild(p.a), e._tempSprite.visible = !1, e.applyTheme(), e
                }

                return Object(o.c)(e, t), Object.defineProperty(e.prototype, "axisLength", {
                    get: function () {
                        return this.polyspline.distance
                    }, enumerable: !0, configurable: !0
                }), e.prototype.updateAxisLine = function () {
                    this.line.path = this.polyspline.path
                }, Object.defineProperty(e.prototype, "polyspline", {
                    get: function () {
                        var t = this.getPropertyValue("polyspline");
                        return t || ((t = this.createChild(y.a)).tensionX = 1, t.tensionY = 1, this.polyspline = t), t
                    }, set: function (t) {
                        this.setPropertyValue("polyspline", t, !0), t.parent = this
                    }, enumerable: !0, configurable: !0
                }), Object.defineProperty(e.prototype, "autoScale", {
                    get: function () {
                        return this.getPropertyValue("autoScale")
                    }, set: function (t) {
                        this.setPropertyValue("autoScale", t, !0)
                    }, enumerable: !0, configurable: !0
                }), Object.defineProperty(e.prototype, "autoCenter", {
                    get: function () {
                        return this.getPropertyValue("autoCenter")
                    }, set: function (t) {
                        this.setPropertyValue("autoCenter", t, !0)
                    }, enumerable: !0, configurable: !0
                }), Object.defineProperty(e.prototype, "precisionStep", {
                    get: function () {
                        return this.getPropertyValue("precisionStep")
                    }, set: function (t) {
                        this.setPropertyValue("precisionStep", t, !0)
                    }, enumerable: !0, configurable: !0
                }), Object.defineProperty(e.prototype, "points", {
                    get: function () {
                        return this.getPropertyValue("points")
                    }, set: function (t) {
                        this.setPropertyValue("points", t, !0) && (this._pointsChanged = !0, this.polyspline.segments = [t])
                    }, enumerable: !0, configurable: !0
                }), e.prototype.setAxis = function (e) {
                    var i = this;
                    if (t.prototype.setAxis.call(this, e), e && e.chart) {
                        var n = e.chart;
                        this._disposers.push(n.curveContainer.events.on("positionchanged", function () {
                            i.handleSizeChange()
                        })), this._disposers.push(n.events.on("maxsizechanged", function () {
                            i.handleSizeChange()
                        }))
                    }
                }, e.prototype.handleSizeChange = function () {
                    if (this._pointsChanged) {
                        var t = this.axis.getPositionRangePath(0, 1);
                        this._tempSprite.path = t, this._pointsChanged = !1
                    }
                    if (this.points) {
                        var e = this.axis.chart, i = e.curveContainer,
                            n = e.plotContainer.maxWidth - i.pixelPaddingLeft - i.pixelPaddingRight,
                            o = e.plotContainer.maxHeight - i.pixelPaddingTop - i.pixelPaddingBottom,
                            s = this._tempSprite.element.getBBox(), r = {x: 0, y: 0};
                        this.autoCenter && (r = {x: s.x + s.width / 2, y: s.y + s.height / 2});
                        var a = 1;
                        this.autoScale && (a = u.min(n / s.width, o / s.height));
                        var p = [];
                        g.each(this.points, function (t) {
                            p.push({x: (t.x - r.x) * a, y: (t.y - r.y) * a})
                        }), this.polyspline.segments = [p]
                    }
                }, e.prototype.positionToPoint = function (t, e) {
                    var i = this.axis;
                    t = (t - i.start) / (i.end - i.start);
                    var n = this.polyspline.positionToPoint(t, !0);
                    n.angle += 90;
                    var o = this.axisRendererY;
                    if (x.isNumber(e) && o) {
                        var s = o.positionToPoint(e).y;
                        n.x += s * u.cos(n.angle), n.y += s * u.sin(n.angle)
                    }
                    return n
                }, e.prototype.positionToAngle = function (t) {
                    var e = this.axis;
                    return t = u.max(0, (t - e.start) / (e.end - e.start)), this.polyspline.positionToPoint(t, !0).angle + 90
                }, e.prototype.updateGridElement = function (t, e, i) {
                    t.element && (e += (i - e) * t.location, t.zIndex = 0, t.path = this.getGridPath(e), this.toggleVisibility(t, e, 0, 1))
                }, e.prototype.getGridPath = function (t) {
                    var e = this.positionToPoint(t), i = e.angle, n = this.axisRendererY;
                    if (n) {
                        var o = -n.radius, s = -n.innerRadius;
                        return f.moveTo({x: e.x + s * u.cos(i), y: e.y + s * u.sin(i)}) + f.lineTo({
                            x: e.x + o * u.cos(i),
                            y: e.y + o * u.sin(i)
                        })
                    }
                    return ""
                }, e.prototype.updateTickElement = function (t, e) {
                    if (t.element) {
                        var i = this.positionToPoint(e), n = i.angle, o = t.length;
                        t.inside && (o *= -1), t.path = f.moveTo({x: i.x, y: i.y}) + f.lineTo({
                            x: i.x + o * u.cos(n),
                            y: i.y + o * u.sin(n)
                        }), this.toggleVisibility(t, e, 0, 1)
                    }
                }, e.prototype.updateLabelElement = function (t, e, i, n) {
                    x.hasValue(n) || (n = t.location), e += (i - e) * n;
                    var o = this.positionToPoint(e);
                    t.x = o.x, t.y = o.y, t.zIndex = 2, this.toggleVisibility(t, e, this.minLabelPosition, this.maxLabelPosition)
                }, e.prototype.getPositionRangePath = function (t, e) {
                    var i = "", n = this.axisRendererY;
                    if (n) {
                        if (t > e) {
                            var o = t;
                            t = e, e = o
                        }
                        var s = n.axis.start, r = n.axis.end, a = this.axis.start, p = this.axis.end;
                        if (t <= a && e <= a || t >= p && e >= p) return i;
                        if (t = u.fitToRange(t, a, p), (e = u.fitToRange(e, a, p)) == a || t == p) return i;
                        if (e == t) return i;
                        var l = 0 | u.round(n.positionToPoint(s).y, 1), h = 0 | u.round(n.positionToPoint(r).y, 1),
                            c = this.positionToPoint(t), d = c.angle;
                        i = f.moveTo(c);
                        for (var y = Math.ceil(this.axisLength / this.precisionStep * (e - t) / (p - a)), x = 0; x <= y; x++) {
                            var g = t + x / y * (e - t);
                            d = (c = this.positionToPoint(g)).angle;
                            var v = c.x + l * u.cos(d), P = c.y + l * u.sin(d);
                            i += f.lineTo({x: v, y: P})
                        }
                        for (x = y; x >= 0; x--) {
                            g = t + x / y * (e - t);
                            d = (c = this.positionToPoint(g)).angle;
                            v = c.x + h * u.cos(d), P = c.y + h * u.sin(d);
                            i += f.lineTo({x: v, y: P})
                        }
                        i += f.closePath()
                    }
                    return i
                }, e.prototype.updateBaseGridElement = function () {
                }, e.prototype.updateBullet = function (t, e, i) {
                    var n = .5;
                    t instanceof v.a && (n = t.location), e += (i - e) * n;
                    var o = this.positionToPoint(e);
                    t.moveTo({x: o.x, y: o.y}), this.toggleVisibility(t, e, 0, 1)
                }, e.prototype.updateBreakElement = function (t) {
                    var e = this.axisRendererY;
                    if (e) {
                        var i = t.startPosition, n = t.endPosition, o = this.positionToAngle(i),
                            s = this.positionToPoint(i), r = this.positionToAngle(n), a = this.positionToPoint(n),
                            p = t.startLine, l = t.endLine, h = t.fillShape, c = -e.radius + t.pixelMarginTop,
                            d = -e.innerRadius - t.pixelMarginBottom, y = {x: s.x + d * u.cos(o), y: s.y + d * u.sin(o)},
                            x = {x: s.x + c * u.cos(o), y: s.y + c * u.sin(o)},
                            g = {x: a.x + d * u.cos(r), y: a.y + d * u.sin(r)},
                            v = {x: a.x + c * u.cos(r), y: a.y + c * u.sin(r)};
                        p.path = f.moveTo(y) + Object(P.c)(y, x, p.waveLength, p.waveHeight, p.tension, !0), l.path = f.moveTo(v) + Object(P.c)(v, g, l.waveLength, l.waveHeight, l.tension, !0);
                        var m = f.moveTo(y);
                        m += Object(P.c)(y, x, h.waveLength, h.waveHeight, h.tension, !0);
                        for (var b = this.axis.start, C = this.axis.end, A = Math.ceil(this.axisLength / this.precisionStep * (n - i) / (C - b)), T = 0; T <= A; T++) {
                            var R = i + T / A * (n - i), S = (L = this.positionToPoint(R)).angle, O = L.x + c * u.cos(S),
                                V = L.y + c * u.sin(S);
                            m += f.lineTo({x: O, y: V})
                        }
                        m += Object(P.c)(v, g, h.waveLength, h.waveHeight, h.tension, !0);
                        for (T = A; T >= 0; T--) {
                            var L;
                            R = i + T / A * (n - i), S = (L = this.positionToPoint(R)).angle, O = L.x + d * u.cos(S), V = L.y + d * u.sin(S);
                            m += f.lineTo({x: O, y: V})
                        }
                        h.path = m, this.toggleVisibility(t.startLine, t.startPosition, 0, 1), this.toggleVisibility(t.endLine, t.endPosition, 0, 1)
                    }
                }, e.prototype.toAxisPosition = function (t) {
                    return t
                }, e.prototype.coordinateToPosition = function (e, i) {
                    var n = this.polyspline.allPoints, o = this.polyspline.getClosestPointIndex({x: e, y: i});
                    return t.prototype.coordinateToPosition.call(this, o / (n.length - 1) * this.axisLength)
                }, e.prototype.updateTooltip = function () {
                }, Object.defineProperty(e.prototype, "inversed", {
                    get: function () {
                        return !1
                    }, set: function (t) {
                    }, enumerable: !0, configurable: !0
                }), e
            }(d.a);
        a.c.registeredClasses.AxisRendererCurveX = m;
        var b = i("OXm9"), C = i("Vk33"), A = i("hD5A"), T = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e._chart = new A.d, e.className = "AxisRendererCurveY", e.isMeasured = !1, e.minGridDistance = 30, e.isMeasured = !1, e.layout = "none", e.radius = 40, e.innerRadius = -40, e.line.strokeOpacity = 0, e.labels.template.horizontalCenter = "right", e._disposers.push(e._chart), e.applyTheme(), e
            }

            return Object(o.c)(e, t), e.prototype.validate = function () {
                this.chart && this.chart.invalid && this.chart.validate(), t.prototype.validate.call(this)
            }, Object.defineProperty(e.prototype, "axisLength", {
                get: function () {
                    return Math.abs(this.radius - this.innerRadius)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "radius", {
                get: function () {
                    return this.getPropertyValue("radius")
                }, set: function (t) {
                    this.setPropertyValue("radius", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "innerRadius", {
                get: function () {
                    return this.getPropertyValue("innerRadius")
                }, set: function (t) {
                    this.setPropertyValue("innerRadius", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "chart", {
                get: function () {
                    return this._chart.get()
                }, set: function (t) {
                    this._chart.set(t, null)
                }, enumerable: !0, configurable: !0
            }), e.prototype.positionToPoint = function (t) {
                return {x: 0, y: this.positionToCoordinate(t)}
            }, e.prototype.updateAxisLine = function () {
                var t = this.axisRendererX;
                if (t) {
                    var e = t.axis, i = t.positionToPoint(e.start + (e.end - e.start) * this.axisLocation), n = i.angle,
                        o = -this.radius, s = -this.innerRadius;
                    this.line.path = f.moveTo({x: s * u.cos(n), y: s * u.sin(n)}) + f.lineTo({
                        x: o * u.cos(n),
                        y: o * u.sin(n)
                    }), this.line.moveTo(i);
                    var r = this.axis.title;
                    r.moveTo({x: i.x + o / 2 * u.cos(n), y: i.y + o / 2 * u.sin(n)}), r.rotation = n - 180
                }
            }, e.prototype.updateGridElement = function (t, e, i) {
                this.axisRendererX && ((e += (i - e) * t.location) >= 0 && e <= 1 && (t.path = this.getGridPath(e)), this.positionItem(t, {
                    x: 0,
                    y: 0
                }), this.toggleVisibility(t, e, 0, 1))
            }, e.prototype.getGridPath = function (t) {
                var e = this.axisRendererX, i = "";
                if (e && x.isNumber(t)) {
                    for (var n = u.round(this.positionToPoint(t).y, 1), o = e.positionToPoint(e.axis.start), s = o.angle, r = Math.ceil(e.axisLength / e.precisionStep), a = e.axis.start, p = e.axis.end, l = 0; l <= r; l++) {
                        var h = a + l / r * (p - a);
                        s = (o = e.positionToPoint(h)).angle;
                        var c = o.x + n * u.cos(s), d = o.y + n * u.sin(s);
                        i += f.lineTo({x: c, y: d})
                    }
                    i = i.replace("L", "M")
                }
                return i
            }, e.prototype.updateLabelElement = function (t, e, i, n) {
                x.hasValue(n) || (n = t.location), e += (i - e) * n;
                var o = this.positionToPoint(e).y, s = this.axisRendererX;
                if (s) {
                    var r = s.axis, a = s.positionToPoint(r.start + (r.end - r.start) * this.axisLocation), p = a.angle;
                    a.x += o * u.cos(p), a.y += o * u.sin(p), this.positionItem(t, a), this.toggleVisibility(t, e, this.minLabelPosition, this.maxLabelPosition)
                }
            }, e.prototype.updateTickElement = function (t, e) {
                if (t.element) {
                    var i = this.axisRendererX;
                    if (i) {
                        var n = i.positionToPoint(this.axisLocation), o = n.angle, s = this.positionToPoint(e).y;
                        n.x += s * u.cos(o), n.y += s * u.sin(o), (o = u.normalizeAngle(o + 90)) / 90 != Math.round(o / 90) ? t.pixelPerfect = !1 : t.pixelPerfect = !0;
                        var r = t.length;
                        t.inside && (r *= -1), t.path = f.moveTo({x: 0, y: 0}) + f.lineTo({
                            x: r * u.cos(o),
                            y: r * u.sin(o)
                        }), this.positionItem(t, n), this.toggleVisibility(t, e, 0, 1)
                    }
                }
            }, e.prototype.updateBullet = function (t, e, i) {
                var n = .5;
                t instanceof v.a && (n = t.location), e += (i - e) * n;
                var o = this.axisRendererX;
                if (o) {
                    var s = o.positionToPoint(this.axisLocation), r = s.angle, a = this.positionToPoint(e).y;
                    s.x += a * u.cos(r), s.y += a * u.sin(r), r = u.normalizeAngle(r + 90), this.positionItem(t, s), this.toggleVisibility(t, e, 0, 1)
                }
            }, e.prototype.updateBaseGridElement = function () {
            }, e.prototype.fitsToBounds = function (t) {
                return !0
            }, e.prototype.getPositionRangePath = function (t, e) {
                var i = "", n = this.axisRendererX;
                if (n) {
                    var o = n.axis.start, s = n.axis.end, r = this.axis.start, a = this.axis.end;
                    if (t <= r && e <= r || t >= a && e >= a) return i;
                    t = u.fitToRange(t, r, a), e = u.fitToRange(e, r, a);
                    var p = u.round(this.positionToPoint(t).y, 1), l = u.round(this.positionToPoint(e).y, 1);
                    if (x.isNaN(p) || x.isNaN(l)) return "";
                    var h = n.positionToPoint(o), c = h.angle;
                    i = f.moveTo(h);
                    for (var d = Math.ceil(n.axisLength / n.precisionStep), y = 0; y <= d; y++) {
                        var g = o + y / d * (s - o);
                        c = (h = n.positionToPoint(g)).angle;
                        var v = h.x + p * u.cos(c), P = h.y + p * u.sin(c);
                        i += f.lineTo({x: v, y: P})
                    }
                    for (y = d; y >= 0; y--) {
                        g = o + y / d * (s - o);
                        c = (h = n.positionToPoint(g)).angle;
                        v = h.x + l * u.cos(c), P = h.y + l * u.sin(c);
                        i += f.lineTo({x: v, y: P})
                    }
                    i += f.closePath()
                }
                return i
            }, e.prototype.updateBreakElement = function (t) {
                this.axisRendererX && (t.fillShape.path = this.getPositionRangePath(t.startPosition, t.endPosition), this.toggleVisibility(t.startLine, t.startPosition, 0, 1), this.toggleVisibility(t.endLine, t.endPosition, 0, 1))
            }, e.prototype.createBreakSprites = function (t) {
                t.startLine = new C.a, t.endLine = new C.a, t.fillShape = new C.a
            }, e.prototype.updateTooltip = function () {
                this.axis
            }, e.prototype.positionToCoordinate = function (t) {
                var e, i = this.axis, n = i.axisFullLength;
                return e = i.renderer.inversed ? (i.end - t) * n : (t - i.start) * n, u.round(-this.innerRadius - e, 4)
            }, Object.defineProperty(e.prototype, "axisLocation", {
                get: function () {
                    return this.getPropertyValue("axisLocation")
                }, set: function (t) {
                    this.setPropertyValue("axisLocation", t), this.invalidateAxisItems()
                }, enumerable: !0, configurable: !0
            }), e.prototype.processRenderer = function () {
                t.prototype.processRenderer.call(this);
                var e = this.axis;
                if (e) {
                    var i = e.title;
                    i && (i.isMeasured = !1, i.horizontalCenter = "middle", i.verticalCenter = "bottom")
                }
            }, e.prototype.coordinateToPosition = function (e, i) {
                var n = this.axisRendererX, o = e;
                if (n) {
                    var s = n.polyspline.allPoints[n.polyspline.getClosestPointIndex({x: i, y: e})], r = s.angle - 90;
                    o = u.getDistance({
                        x: s.x + this.innerRadius * u.cos(r),
                        y: s.y + this.innerRadius * u.sin(r)
                    }, {x: i, y: e})
                }
                return t.prototype.coordinateToPosition.call(this, o)
            }, e.prototype.toAxisPosition = function (t) {
                return t
            }, e
        }(b.a);
        a.c.registeredClasses.AxisRendererCurveY = T;
        var R = i("Q4nc"), S = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveChartDataItem", e.applyTheme(), e
            }

            return Object(o.c)(e, t), e
        }(s.b), O = function (t) {
            function e() {
                var e = t.call(this) || this;
                e._axisRendererX = m, e._axisRendererY = T, e.className = "CurveChart";
                var i = e.plotContainer.createChild(c.a);
                return i.shouldClone = !1, i.layout = "absolute", i.align = "center", i.valign = "middle", e.seriesContainer.parent = i, e.curveContainer = i, e.bulletsContainer.parent = i, e.axisBulletsContainer.parent = i, e._cursorContainer = i, e._bulletMask = void 0, e.applyTheme(), e
            }

            return Object(o.c)(e, t), e.prototype.applyInternalDefaults = function () {
                t.prototype.applyInternalDefaults.call(this), x.hasValue(this.readerTitle)
            }, e.prototype.processAxis = function (e) {
                t.prototype.processAxis.call(this, e);
                var i = e.renderer;
                i.gridContainer.parent = i, i.breakContainer.parent = i, e.parent = this.curveContainer, i.toBack()
            }, e.prototype.processConfig = function (e) {
                if (e && (x.hasValue(e.cursor) && !x.hasValue(e.cursor.type) && (e.cursor.type = "CurveCursor"), x.hasValue(e.series) && x.isArray(e.series))) for (var i = 0, n = e.series.length; i < n; i++) e.series[i].type = e.series[i].type || "CurveLineSeries";
                t.prototype.processConfig.call(this, e)
            }, e.prototype.createSeries = function () {
                return new h
            }, e.prototype.updateXAxis = function (t) {
                t && t.processRenderer()
            }, e.prototype.updateYAxis = function (t) {
                t && t.processRenderer()
            }, e.prototype.hasLicense = function () {
                if (!t.prototype.hasLicense.call(this)) return !1;
                for (var e = 0; e < R.a.licenses.length; e++) if (R.a.licenses[e].match(/^TL.{5,}/i)) return !0;
                return !1
            }, e
        }(s.a);
        a.c.registeredClasses.CurveChart = O;
        var V = i("tjMS"), L = i("v9UT"), j = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "SerpentineChartDataItem", e.applyTheme(), e
            }

            return Object(o.c)(e, t), e
        }(S), I = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "SerpentineChart", e.orientation = "vertical", e.levelCount = 3, e.yAxisRadius = Object(V.c)(25), e.yAxisInnerRadius = Object(V.c)(-25), e.applyTheme(), e
            }

            return Object(o.c)(e, t), Object.defineProperty(e.prototype, "orientation", {
                get: function () {
                    return this.getPropertyValue("orientation")
                }, set: function (t) {
                    this.setPropertyValue("orientation", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "levelCount", {
                get: function () {
                    return this.getPropertyValue("levelCount")
                }, set: function (t) {
                    this.setPropertyValue("levelCount", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "yAxisRadius", {
                get: function () {
                    return this.getPropertyValue("yAxisRadius")
                }, set: function (t) {
                    this.setPropertyValue("yAxisRadius", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "yAxisInnerRadius", {
                get: function () {
                    return this.getPropertyValue("yAxisInnerRadius")
                }, set: function (t) {
                    this.setPropertyValue("yAxisInnerRadius", t, !0)
                }, enumerable: !0, configurable: !0
            }), e.prototype.validate = function () {
                t.prototype.validate.call(this);
                var e = this.curveContainer, i = this.plotContainer.maxWidth - e.pixelPaddingLeft - e.pixelPaddingRight,
                    n = this.plotContainer.maxHeight - e.pixelPaddingTop - e.pixelPaddingBottom, o = 0;
                this.yAxes.each(function (t) {
                    o = u.max(t.renderer.radius, o)
                }), i -= 2 * o, n -= 2 * o;
                var s, r = [], a = this.levelCount;
                if ("vertical" == this.orientation) {
                    s = u.min(n / (a - 1) / 2, i / 2), n = u.min(s * (a - 1) * 2, n);
                    for (var p = 0; p < this.levelCount; p++) if (p % 2 == 0) {
                        r.push({x: -i / 2 + s, y: -n / 2 + n / (a - 1) * p}), r.push({
                            x: i / 2 - s,
                            y: -n / 2 + n / (a - 1) * p
                        });
                        var l = {x: i / 2 - s, y: -n / 2 + n / (a - 1) * (p + .5)};
                        if (p < this.levelCount - 1) for (var h = 0; h < 50; h++) {
                            var c = h / 50 * 180 - 90;
                            r.push({x: l.x + s * u.cos(c), y: l.y + s * u.sin(c)})
                        }
                    } else {
                        r.push({x: i / 2 - s, y: -n / 2 + n / (a - 1) * p}), r.push({
                            x: -i / 2 + s,
                            y: -n / 2 + n / (a - 1) * p
                        });
                        l = {x: -i / 2 + s, y: -n / 2 + n / (a - 1) * (p + .5)};
                        if (p < this.levelCount - 1) for (var d = 0; d < 50; d++) {
                            c = -90 - d / 50 * 180;
                            r.push({x: l.x + s * u.cos(c), y: l.y + s * u.sin(c)})
                        }
                    }
                } else {
                    s = u.min(i / (a - 1) / 2, n / 2), i = u.min(s * (a - 1) * 2, i);
                    for (p = 0; p < this.levelCount; p++) if (p % 2 == 0) {
                        r.push({y: -n / 2 + s, x: -i / 2 + i / (a - 1) * p}), r.push({
                            y: n / 2 - s,
                            x: -i / 2 + i / (a - 1) * p
                        });
                        l = {y: n / 2 - s, x: -i / 2 + i / (a - 1) * (p + .5)};
                        if (p < this.levelCount - 1) for (var y = 0; y < 50; y++) {
                            c = y / 50 * 180 - 90;
                            r.push({y: l.y + s * u.cos(c), x: l.x + s * u.sin(c)})
                        }
                    } else {
                        r.push({y: n / 2 - s, x: -i / 2 + i / (a - 1) * p}), r.push({
                            y: -n / 2 + s,
                            x: -i / 2 + i / (a - 1) * p
                        });
                        l = {y: -n / 2 + s, x: -i / 2 + i / (a - 1) * (p + .5)};
                        if (p < this.levelCount - 1) for (var f = 0; f < 50; f++) {
                            c = -90 - f / 50 * 180;
                            r.push({y: l.y + s * u.cos(c), x: l.x + s * u.sin(c)})
                        }
                    }
                }
                this.xAxes.each(function (t) {
                    t.renderer.points = r, t.renderer.autoScale = !1, t.renderer.autoCenter = !1, t.renderer.polyspline.tensionX = 1, t.renderer.polyspline.tensionY = 1
                });
                var x = L.relativeRadiusToValue(this.yAxisInnerRadius, 2 * s),
                    g = L.relativeRadiusToValue(this.yAxisRadius, 2 * s);
                this.yAxes.each(function (t) {
                    t.renderer.radius = g, t.renderer.innerRadius = x
                })
            }, e.prototype.updateYAxis = function (e) {
                t.prototype.updateYAxis.call(this, e), e.innerRadius = void 0, e.radius = void 0
            }, e
        }(O);
        a.c.registeredClasses.SerpentineChart = I;
        var X = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "SpiralChartDataItem", e.applyTheme(), e
            }

            return Object(o.c)(e, t), e
        }(S), Y = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "SpiralChart", e.levelCount = 3, e.precisionStep = 5, e.startAngle = 0, e.endAngle = 0, e.innerRadius = Object(V.c)(25), e.yAxisRadius = Object(V.c)(35), e.yAxisInnerRadius = Object(V.c)(-35), e.inversed = !1, e.applyTheme(), e
            }

            return Object(o.c)(e, t), Object.defineProperty(e.prototype, "levelCount", {
                get: function () {
                    return this.getPropertyValue("levelCount")
                }, set: function (t) {
                    this.setPropertyValue("levelCount", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "startAngle", {
                get: function () {
                    return this.getPropertyValue("startAngle")
                }, set: function (t) {
                    this.setPropertyValue("startAngle", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "endAngle", {
                get: function () {
                    return this.getPropertyValue("endAngle")
                }, set: function (t) {
                    this.setPropertyValue("endAngle", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "radiusStep", {
                get: function () {
                    return this.getPropertyValue("radiusStep")
                }, set: function (t) {
                    this.setPropertyValue("radiusStep", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "precisionStep", {
                get: function () {
                    return this.getPropertyValue("precisionStep")
                }, set: function (t) {
                    this.setPropertyValue("precisionStep", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "innerRadius", {
                get: function () {
                    return this.getPropertyValue("innerRadius")
                }, set: function (t) {
                    this.setPropertyValue("innerRadius", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "yAxisRadius", {
                get: function () {
                    return this.getPropertyValue("yAxisRadius")
                }, set: function (t) {
                    this.setPropertyValue("yAxisRadius", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "yAxisInnerRadius", {
                get: function () {
                    return this.getPropertyValue("yAxisInnerRadius")
                }, set: function (t) {
                    this.setPropertyValue("yAxisInnerRadius", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "inversed", {
                get: function () {
                    return this.getPropertyValue("inversed")
                }, set: function (t) {
                    this.setPropertyValue("inversed", t, !0)
                }, enumerable: !0, configurable: !0
            }), e.prototype.validate = function () {
                t.prototype.validate.call(this);
                var e = this.curveContainer, i = this.plotContainer.maxWidth - e.pixelPaddingLeft - e.pixelPaddingRight,
                    n = this.plotContainer.maxHeight - e.pixelPaddingTop - e.pixelPaddingBottom, o = u.min(i, n) / 2,
                    s = this.radiusStep, r = L.relativeRadiusToValue(this.innerRadius, o);
                x.isNumber(s) || (s = (o - r) / this.levelCount);
                var a = f.spiralPoints(0, 0, o, o, r, this.precisionStep, s, this.startAngle, this.endAngle),
                    p = L.relativeRadiusToValue(this.yAxisInnerRadius, s),
                    l = L.relativeRadiusToValue(this.yAxisRadius, s);
                this.inversed && a.reverse(), this.xAxes.each(function (t) {
                    t.renderer.points = a, t.renderer.autoScale = !1, t.renderer.autoCenter = !1, t.renderer.polyspline.tensionX = 1, t.renderer.polyspline.tensionY = 1
                }), this.yAxes.each(function (t) {
                    t.renderer.radius = l, t.renderer.innerRadius = p
                })
            }, e.prototype.updateYAxis = function (e) {
                t.prototype.updateYAxis.call(this, e), e.innerRadius = void 0, e.radius = void 0
            }, e
        }(O);
        a.c.registeredClasses.SpiralChart = Y;
        var k = i("5vid"), M = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveColumn", e
            }

            return Object(o.c)(e, t), e.prototype.createAssets = function () {
                this.curveColumn = this.createChild(p.a), this.CurveColumn = this.curveColumn, this.curveColumn.shouldClone = !1, this.curveColumn.strokeOpacity = void 0, this.column = this.curveColumn
            }, e.prototype.copyFrom = function (e) {
                t.prototype.copyFrom.call(this, e), this.curveColumn && this.curveColumn.copyFrom(e.curveColumn)
            }, e
        }(i("DG6Q").a);
        a.c.registeredClasses.CurveColumn = M;
        var w = i("pR7v"), _ = i("VB2N"), N = i("Qkdp"), D = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "ColumnSeriesDataItem", e.applyTheme(), e
            }

            return Object(o.c)(e, t), e
        }(k.b), B = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveColumnSeries", e.bulletsContainer.mask = new p.a, e.topOffset = .2, e.bottomOffset = .2, e.applyTheme(), e
            }

            return Object(o.c)(e, t), e.prototype.createColumnTemplate = function () {
                return new M
            }, e.prototype.validateDataElementReal = function (t) {
                var e = this, i = this.yField, n = this.yOpenField, o = this.xField, s = this.xOpenField,
                    r = this.getStartLocation(t), a = this.getEndLocation(t), l = this.columns.template,
                    h = l.percentWidth, c = l.percentHeight;
                x.isNaN(h) && (h = 100);
                var d, y = [], v = this.xAxis, P = this.yAxis, m = v.renderer;
                if (this.baseAxis == this.xAxis) {
                    r += S = u.round((a - r) * (1 - h / 100) / 2, 5);
                    var b = ((a -= S) - r) / (Math.ceil(this.xAxis.axisLength / m.precisionStep / (this.endIndex - this.startIndex)) + 2),
                        C = t.locations[n], A = t.locations[i];
                    if (this.yAxis instanceof w.a) this.dataFields[this.yField] != this.dataFields[this.yOpenField] && (C = 0, A = 0); else if (this.yAxis instanceof _.a && !x.isNaN(c)) {
                        A = 0, C = 1;
                        var T = u.round((1 - c / 100) / 2, 5);
                        A += T, C -= T
                    }
                    for (var R = r; R <= a; R += b) R > a && (R = a), y.push(this.getPoint(t, o, i, R, A));
                    y.push(this.getPoint(t, o, i, a, A));
                    for (R = a; R >= r; R -= b) R < r && (R = r), y.push(this.getPoint(t, s, n, R, C));
                    y.push(this.getPoint(t, s, n, r, C)), d = this.getPoint(t, o, i, r + (a - r) / 2, .5)
                } else {
                    var S;
                    r += S = u.round((a - r) * (1 - c / 100) / 2, 5), a -= S;
                    var O = {start: v.start, end: v.end}, V = {start: P.start, end: P.end}, L = t.locations[o],
                        j = t.locations[s];
                    this.xAxis instanceof w.a && this.dataFields[this.xField] != this.dataFields[this.xOpenField] && (L = 0, j = 0);
                    var I = v.getPositionX(t, s, j, "valueX", O), X = v.getPositionX(t, o, L, "valueX", O),
                        Y = P.getPositionY(t, n, r, "valueY", V), k = P.getPositionY(t, i, a, "valueY", V);
                    b = (X - I) / (Math.ceil(v.axisLength / m.precisionStep * (X - I) / (v.end - v.start)) + 2);
                    if (X > I) {
                        for (R = I; R <= X; R += b) R > X && (R = X), y.push(v.renderer.positionToPoint(R, Y));
                        y.push(v.renderer.positionToPoint(X, Y));
                        for (R = X; R >= I; R -= b) R < I && (R = I), y.push(v.renderer.positionToPoint(R, k));
                        y.push(v.renderer.positionToPoint(I, k))
                    }
                    d = v.renderer.positionToPoint(I + (X - I) / 2, Y + (k - Y) / 2)
                }
                var M = t.column;
                M || (M = this.columns.create(), N.copyProperties(this, M, p.b), N.copyProperties(this.columns.template, M, p.b), t.column = M, t.addSprite(M), this.setColumnStates(M), M.paper = this.paper);
                var D = M.curveColumn;
                y.length > 0 && y.push(y[0]), D.path = f.pointsToPath(y), M.__disabled = !1, M.parent = this.columnsContainer, M.tooltipX = d.x, M.tooltipY = d.y, M.curveColumn.tooltipX = d.x, M.curveColumn.tooltipY = d.y, this.axisRanges.each(function (i) {
                    var n = t.rangesColumns.getKey(i.uid);
                    n || ((n = e.columns.create()).dataItem && g.remove(n.dataItem.sprites, n), t.addSprite(n), n.paper = e.paper, e.setColumnStates(n), t.rangesColumns.setKey(i.uid, n)), n.curveColumn.path = D.path, n.__disabled = !1, n.parent = i.contents
                })
            }, e.prototype.getPoint = function (t, e, i, n, o, s, r) {
                s || (s = "valueX"), r || (r = "valueY");
                var a = this.yAxis.renderer,
                    p = u.fitToRange(this.yAxis.getY(t, i, o, r), -a.radius * (1 + this.topOffset), -a.innerRadius * (1 + this.bottomOffset)),
                    l = {start: this.xAxis.start, end: this.xAxis.end}, h = this.xAxis.getX(t, e, n, s, l),
                    c = this.xAxis.getY(t, e, n, s, l), d = this.xAxis.getAngle(t, e, n, s, l);
                return {x: h + p * u.cos(d), y: c + p * u.sin(d)}
            }, e.prototype.getMaskPath = function () {
                var t = this.yAxis.renderer, e = t.getPositionRangePath(t.axis.start, t.axis.end),
                    i = this.bulletsContainer;
                return this.chart && this.chart.maskBullets ? (i.mask || (i.mask = new p.a), i.mask.path = e) : i.mask = void 0, e
            }, Object.defineProperty(e.prototype, "topOffset", {
                get: function () {
                    return this.getPropertyValue("topOffset")
                }, set: function (t) {
                    this.setPropertyValue("topOffset", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "bottomOffset", {
                get: function () {
                    return this.getPropertyValue("bottomOffset")
                }, set: function (t) {
                    this.setPropertyValue("bottomOffset", t, !0)
                }, enumerable: !0, configurable: !0
            }), e.prototype.positionBulletReal = function (t, e, i) {
                var n = this.xAxis, o = this.yAxis;
                (e < n.start || e > n.end || i < o.start || i > o.end) && (t.visible = !1), t.moveTo(this.xAxis.renderer.positionToPoint(e, i))
            }, e.prototype.setXAxis = function (e) {
                t.prototype.setXAxis.call(this, e), this.updateRendererRefs()
            }, e.prototype.setYAxis = function (e) {
                t.prototype.setYAxis.call(this, e), this.updateRendererRefs()
            }, e.prototype.updateRendererRefs = function () {
                var t = this.xAxis.renderer, e = this.yAxis.renderer;
                t.axisRendererY = e, e.axisRendererX = t
            }, e
        }(k.a);
        a.c.registeredClasses.CurveColumnSeries = B, a.c.registeredClasses.CurveColumnSeriesDataItem = D;
        var E = i("KknQ"), F = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveStepLineSeriesDataItem", e.applyTheme(), e
            }

            return Object(o.c)(e, t), e
        }(l), G = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveStepLineSeries", e.startLocation = 0, e.endLocation = 1, e.applyTheme(), e
            }

            return Object(o.c)(e, t), e.prototype.createDataItem = function () {
                return new F
            }, e.prototype.addPoints = function (t, e, i, n, o) {
                var s = this.startLocation, r = this.endLocation;
                o && (s = this.endLocation, r = this.startLocation);
                var a = this.xAxis, p = this.yAxis, u = this._previousPosition, l = {start: a.start, end: a.end},
                    h = {start: p.start, end: p.end}, c = a.renderer;
                if (this.baseAxis == this.xAxis) {
                    var d = Math.ceil(this.xAxis.axisLength / c.precisionStep / (this.endIndex - this.startIndex)) + 2,
                        y = Math.abs(r - s) / d;
                    if (this.xAxis instanceof E.a) {
                        var f = e.index;
                        if (o) {
                            var g = this.dataItems.getIndex(f - 1), v = this.xAxis.baseDuration;
                            if (g) (P = g.dateX.getTime()) < (m = e.dateX.getTime()) - v && (r -= (m - P) / v - 1)
                        } else {
                            var P, m;
                            g = this.dataItems.getIndex(f + 1), v = this.xAxis.baseDuration;
                            if (g) (P = g.dateX.getTime()) > (m = e.dateX.getTime()) + v && (r += (P - m) / v - 1)
                        }
                    }
                    if (o) {
                        for (var b = s; b >= r; b -= y) b < r && (b = r), t.push(this.getPoint(e, i, n, b, e.locations[n]));
                        t.push(this.getPoint(e, i, n, r, e.locations[n]))
                    } else {
                        for (b = s; b <= r; b += y) b > r && (b = r), t.push(this.getPoint(e, i, n, b, e.locations[n]));
                        t.push(this.getPoint(e, i, n, r, e.locations[n]))
                    }
                } else {
                    var C = a.getPositionX(e, i, e.locations[i], "valueX", l), A = p.getPositionY(e, n, s, "valueY", h);
                    if (x.isNumber(u)) {
                        d = Math.ceil(a.axisLength / c.precisionStep * (C - u) / (a.end - a.start)) + 2, y = Math.abs((C - u) / d);
                        if (C > u) for (b = u; b <= C; b += y) b > C && (b = C), t.push(a.renderer.positionToPoint(b, A)); else if (C < u) for (b = u; b >= C; b -= y) b < C && (b = C), t.push(a.renderer.positionToPoint(b, A))
                    }
                    var T = this.getPoint(e, i, n, e.locations[i], s);
                    T && t.push(T);
                    var R = this.getPoint(e, i, n, e.locations[i], r);
                    R && t.push(R), this._previousPosition = C
                }
            }, Object.defineProperty(e.prototype, "startLocation", {
                get: function () {
                    return this.getPropertyValue("startLocation")
                }, set: function (t) {
                    this.setPropertyValue("startLocation", t, !0)
                }, enumerable: !0, configurable: !0
            }), Object.defineProperty(e.prototype, "endLocation", {
                get: function () {
                    return this.getPropertyValue("endLocation")
                }, set: function (t) {
                    this.setPropertyValue("endLocation", t, !0)
                }, enumerable: !0, configurable: !0
            }), e
        }(h);
        a.c.registeredClasses.CurveStepLineSeries = G, a.c.registeredClasses.CurveStepLineSeriesDataItem = F;
        var z = function (t) {
            function e() {
                var e = t.call(this) || this;
                return e.className = "CurveCursor", e.applyTheme(), e.mask = void 0, e
            }

            return Object(o.c)(e, t), e.prototype.fitsToBounds = function (t) {
                if (this.xAxis && this.yAxis) {
                    var e = this.xAxis.renderer, i = this.yAxis.renderer, n = e.polyspline.getClosestPointIndex(t),
                        o = u.getDistance(t, e.polyspline.allPoints[n]);
                    return !(o >= Math.abs(i.radius) && o >= Math.abs(i.innerRadius))
                }
            }, e.prototype.triggerMoveReal = function (e) {
                this.xAxis && (!this.xAxis || this.xAxis.cursorTooltipEnabled && !this.xAxis.tooltip.disabled) || this.updateLineX(this.point), this.yAxis && (!this.yAxis || this.yAxis.cursorTooltipEnabled && !this.yAxis.tooltip.disabled) || this.updateLineY(this.point), this.updateSelection(), t.prototype.triggerMoveReal.call(this, e)
            }, e.prototype.updateLineX = function (t) {
                var e = this.lineX, i = this.xAxis;
                if (i || (this.xAxis = this.chart.xAxes.getIndex(0), i = this.xAxis), e && e.visible && !e.disabled && i) {
                    var n = i.renderer.pointToPosition(t), o = i.renderer;
                    e.path = o.getGridPath(u.fitToRange(n, i.start, i.end))
                }
            }, e.prototype.updateLineY = function (t) {
                var e = this.lineY, i = this.yAxis;
                if (i || (this.yAxis = this.chart.yAxes.getIndex(0), i = this.yAxis), e && e.visible && !e.disabled && i) {
                    var n = i.renderer.pointToPosition(t), o = i.renderer;
                    e.path = o.getGridPath(u.fitToRange(n, i.start, i.end))
                }
            }, e.prototype.updateSelection = function () {
                if (this._usesSelection) {
                    var t = this.downPoint, e = this.xAxis, i = this.yAxis;
                    if (e && i && t) {
                        var n = this.point, o = this.selection;
                        o.x = 0, o.y = 0;
                        var s = "", r = this.behavior;
                        if ("zoomX" == r || "selectX" == r) {
                            var a = e.renderer.pointToPosition(t), p = e.renderer.pointToPosition(n);
                            s += e.renderer.getPositionRangePath(a, p), a = e.toGlobalPosition(a), p = e.toGlobalPosition(p), this.xRange = {
                                start: Math.min(a, p),
                                end: Math.max(p, a)
                            }
                        } else if ("zoomY" == r || "selectY" == r) {
                            a = i.renderer.pointToPosition(t), p = i.renderer.pointToPosition(n);
                            s += i.renderer.getPositionRangePath(a, p), a = i.toGlobalPosition(a), p = i.toGlobalPosition(p), this.yRange = {
                                start: Math.min(a, p),
                                end: Math.max(p, a)
                            }
                        }
                        o.path = s
                    } else this.selection.hide()
                }
            }, e.prototype.getPositions = function () {
                this.xAxis && (this.xPosition = this.xAxis.toGlobalPosition(this.xAxis.renderer.pointToPosition(this.point))), this.yAxis && (this.yPosition = this.yAxis.toGlobalPosition(this.yAxis.renderer.pointToPosition(this.point)))
            }, e.prototype.updatePoint = function (t) {
            }, e.prototype.handleXTooltipPosition = function (t) {
                if (this.xAxis.cursorTooltipEnabled) {
                    var e = this.xAxis.tooltip;
                    this.updateLineX(L.svgPointToSprite({x: e.pixelX, y: e.pixelY}, this))
                }
            }, e.prototype.handleYTooltipPosition = function (t) {
                if (this.yAxis.cursorTooltipEnabled) {
                    var e = this.yAxis.tooltip;
                    this.updateLineY(L.svgPointToSprite({x: e.pixelX, y: e.pixelY}, this))
                }
            }, e.prototype.updateLinePositions = function (t) {
            }, e.prototype.getRanges = function () {
            }, e.prototype.updateSize = function () {
            }, e.prototype.fixPoint = function (t) {
                return t
            }, e
        }(i("gqvf").a);
        a.c.registeredClasses.CurveCursor = z, window.am4plugins_timeline = n
    }
}, ["vlr4"]);